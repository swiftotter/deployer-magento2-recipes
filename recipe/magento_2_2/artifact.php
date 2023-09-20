<?php

declare(strict_types=1);

/* (c) Juan Alonso <juan.jalogut@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deployer;

use Deployer\Exception\GracefulShutdownException;

set('artifact_file', 'artifact.tar.gz');
set('artifact_dir', 'artifacts');
set('artifact_excludes_file', __DIR__ . '/../../config/artifact.excludes');

set('artifact_path', function () {
    if (!test('[ -d {{artifact_dir}} ]')) {
        run('mkdir {{artifact_dir}}');
    }
    return get('artifact_dir') . '/' . get('artifact_file');
});

task('artifact:package', function() {
    if (!test('[ -f {{artifact_excludes_file}} ]')) {
        throw new GracefulShutdownException(
            "No artifact excludes file provided, provide one at artifacts/excludes or change location"
        );
    }
    run('tar --exclude-from={{artifact_excludes_file}} -czf {{artifact_path}} -C {{magento_dir}} .');
});

task('artifact:upload', function () {
    $artifactPath = get('artifact_path');
    if (!is_string($artifactPath)) {
        throw new \RuntimeException('Invalid {{artifact_path}}');
    }
    upload($artifactPath, '{{release_path}}', ['options' => ['--bwlimit=4096']]);
});

task('artifact:extract', function () {
    run(
        'tar -xzpf {{release_path}}/{{artifact_file}} -C {{release_path}};'
        . 'rm -rf {{release_path}}/{{artifact_file}}'
    );
});
