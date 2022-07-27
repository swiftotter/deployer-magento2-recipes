<?php

declare(strict_types=1);

namespace Deployer;

require_once(__DIR__ . '/magento_2_1/files.php');

/**
 * Task `qa:build` verify the code does not break the build process (valid Composer, DI compilation and Static assets)
 */
desc('Magento 2 Quality Assurance');
task('qa:build', function () {
    set('deploy_path', '.');
    set('release_path', '.');
    set('current_path', '.');
    $origStaticOptions = get('static_deploy_options');
    set('static_deploy_options', '-f ' . $origStaticOptions);

    invoke('composer:validate');
    invoke('files:compile');
    invoke('files:static_assets');
});

/**
 * Execute Composer file validation
 *
 * @see https://getcomposer.org/doc/03-cli.md#validate
 */
desc('Validate Composer Files');
task('composer:validate', function () {
    run('{{bin/composer}} validate --no-check-all --no-check-publish');
});
