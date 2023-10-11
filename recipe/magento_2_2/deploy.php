<?php declare(strict_types=1);

namespace Deployer;

set('magento_deploy_mode', 'production');

set('current_deploy_mode', function () {
    return run('{{bin/php}} {{release_path}}/{{magento_bin}} deploy:mode:show --no-interaction');
});

task('deploy:mode:set', function () {
    if (empty(get('magento_deploy_mode'))) {
        throw new \InvalidArgumentException('Missing `magento_deploy_mode` configuration!');
    }

    run('{{bin/php}} {{release_path}}/{{magento_bin}} deploy:mode:set {{magento_deploy_mode}} --no-interaction --skip-compilation');
});
