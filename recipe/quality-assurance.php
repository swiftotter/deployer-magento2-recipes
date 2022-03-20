<?php
declare(strict_types=1);

namespace Deployer;

require_once(__DIR__ . '/magento_2_1/files.php');

/**
 * Task `qa:build` verify the code does not break the build process (valid Composer, DI compilation and Static assets)
 */
desc('Magento 2 Quality Assurance');
task('qa:build', [
    'composer:validate',
    'files:compile',
    'files:static_assets'
]);

/**
 * @see https://getcomposer.org/doc/03-cli.md#validate
 */
desc('Validate Composer Files');
task('composer:validate', '{{bin/composer}} validate --no-check-all --no-check-publish');
