<?php

declare(strict_types=1);

namespace Deployer;

// For Magento versions 2.2.6 and after, pre-existing generated directory needs to be cleared.
// Also see https://github.com/jalogut/magento2-deployer-plus/issues/29
task(
    'files:remove-generated',
    function () {
    run('cd {{magento_dir}} && rm -rf generated/*');
    }
);
