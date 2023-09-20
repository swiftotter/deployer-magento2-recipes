<?php

declare(strict_types=1);

/* (c) Juan Alonso <juan.jalogut@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deployer;

set('languages', 'en_US');
set('static_deploy_options', '--exclude-theme=Magento/blank -f -j$(nproc) ');

task('files:compile', function () {
    run('{{bin/php}} {{magento_bin}} setup:di:compile');
});

# Command aims reducing the size of artifact
task('files:cleanup', function () {
    $artifactExclude = array_unique(array_filter((array)get('artifact_exclude')));

    $removeCommand = implode(
        ' ',
        array_map(function ($relativePath) {
            return "{{magento_dir}}/{$relativePath}";
        }, $artifactExclude)
    );

    run("rm -rf $removeCommand");

    # Command removes all Unit, Integration of Mftf tests from the project
    run(
    # Find all "Test" directories in `app/code` and `vendor/`
        'find {{magento_dir}}/app/code/ {{magento_dir}}/vendor/ -type d -name "Test"'
        # Remove all `Integration`, `Unit` or `Mftf` directories below the ones above
        . ' -exec sh -c \'rm -rf "$0/Integration" "$0/Unit" "$0/Mftf" || echo "Cleaned" \' {} \;'
    );
});

task('files:optimize-autoloader', function () {
    run('{{bin/composer}} dump-autoload --no-dev --optimize --apcu');
});
task('files:static_assets', function () {
    run('{{bin/php}} {{magento_bin}} setup:static-content:deploy {{languages}} {{static_deploy_options}}');
});
task('files:permissions', function () {
    run('cd {{magento_dir}} && chmod -R g+w var vendor pub/static pub/media app/etc && chmod u+x bin/magento');
});

task('files:generate', [
    'files:cleanup',
    'files:compile',
    'files:optimize-autoloader',
    'files:static_assets',
    'files:permissions',
]);
