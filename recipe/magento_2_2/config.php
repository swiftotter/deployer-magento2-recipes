<?php

declare(strict_types=1);

/* (c) Juan Alonso <juan.jalogut@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deployer;

use Deployer\Exception\RuntimeException;
use Symfony\Component\Process\Exception\ProcessFailedException;

const CONFIG_UPDATE_NEEDED_EXIT_CODE = 2;

set('config_import_needed', function () {
    try {
        run('{{bin/php}} {{release_path}}/{{magento_bin}} app:config:status --no-interaction', [], null, null, null, null, null, true);
    } catch (ProcessFailedException $e) {
        if (trim($e->getProcess()->getExitCode()) == CONFIG_UPDATE_NEEDED_EXIT_CODE) {
            return true;
        }
        throw $e;
    } catch (RuntimeException $e) {
        if (trim($e->getExitCode()) == CONFIG_UPDATE_NEEDED_EXIT_CODE) {
            return true;
        }
        throw $e;
    }
    return false;
});

task('config:import', function () {
    get('config_import_needed') ?
        run('{{bin/php}} {{release_path}}/{{magento_bin}} app:config:import --no-interaction') :
        writeln('Skipped -> App config is up to date');
});

/*
 * In dev_modules, Magento modules can be specified that should be removed from app/etc/config.php during deployment
 * Modules installed with "require-dev" that are present in app/etc/config.php must be added here to prevent problems
 * with bin/magento setup:db:status
 */
set('dev_modules', []);

desc('Remove dev_modules modules from app/etc/config.php');
task('config:remove-dev-modules', function () {
    $modules = get('dev_modules');
    if (!empty($modules)) {
        $configFile = get('magento_dir') . '/app/etc/config.php';
        $config = include $configFile;
        foreach ($modules as $module) {
            if (isset($config['modules'][$module])) {
                unset($config['modules'][$module]);
                writeln('Removed: ' . $module);
            } else {
                writeln('Not installed: ' . $module);
            }
        }
        $updatedConfig = "<?php\nreturn " . \var_export($config, true) . ";";
        $updatedConfigEof = "\n".$updatedConfig."\nEOF";
        run("cat <<EOF > {{release_path}}/$configFile $updatedConfigEof");
    }
});
