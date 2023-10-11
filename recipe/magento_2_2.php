<?php

declare(strict_types=1);

namespace Deployer;

require_once 'recipe/common.php';
require_once __DIR__ . '/magento_2_1.php';
require_once __DIR__ . '/magento_2_2/artifact.php';
require_once __DIR__ . '/magento_2_2/maintenance.php';
require_once __DIR__ . '/magento_2_2/database.php';
require_once __DIR__ . '/magento_2_2/config.php';
require_once __DIR__ . '/magento_2_2/crontab.php';
require_once __DIR__ . '/magento_2_2/files.php';
require_once __DIR__ . '/magento_2_2/rollback.php';

task('build', ['build:artifact']);
task('build:artifact', [
    'files:remove-generated',
    'deploy:vendors',
    'config:remove-dev-modules',
    'files:generate',
    'artifact:package'
]);

task('build:qa', [
    'files:remove-generated',
    'deploy:vendors',
    'config:remove-dev-modules',
    'files:generate'
]);

task('deploy:artifact', [
    'deploy:info',
    'deploy:setup',
    'deploy:lock',
    'deploy:release',
    'artifact:upload',
    'artifact:extract',
    'deploy:clear_paths',
    'deploy:shared',
    'deploy:writable',
    'maintenance:set:if-needed',
    'cache:clear:if-maintenance',
    'database:upgrade',
    'config:import',
    'deploy:mode:set',
    'deploy:override_shared',
    'deploy:symlink',
    'maintenance:unset',
    'cache:clear',
    'cache:enable',
    'deploy:unlock',
    'deploy:cleanup',
    'deploy:success',
]);
fail('deploy:artifact', 'deploy:failed');
task('deploy', [
    'deploy:info',
    'deploy:setup',
    'deploy:lock',
    'deploy:release',
    'deploy:vendors',
    'config:remove-dev-modules',
    'deploy:clear_paths',
    'deploy:shared',
    'deploy:writable',
    'files:generate',
    'maintenance:set:if-needed',
    'cache:clear:if-maintenance',
    'database:upgrade',
    'config:import',
    'deploy:mode:set',
    'deploy:override_shared',
    'deploy:symlink',
    'maintenance:unset',
    'cache:clear',
    'cache:enable',
    'deploy:unlock',
    'deploy:cleanup',
    'deploy:success',
]);

task('deploy:env', function () {
    $instancePrefix = substr(hash('sha1', microtime()), -5);

    run('rm -f {{release_path}}/app/etc/env.php && cat {{current_path}}/app/etc/env.php > {{release_path}}/app/etc/env.php');
    run("{{bin/php}} {{release_path}}/{{magento_bin}} setup:config:set --no-interaction --cache-id-prefix={$instancePrefix}_ --page-cache-id-prefix={$instancePrefix}_");
});
