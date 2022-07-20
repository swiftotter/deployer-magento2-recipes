<?php

declare(strict_types=1);

/* (c) Juan Alonso <juan.jalogut@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deployer;

set('artifact_file', 'artifact.tar.gz');
set('artifact_dir', 'artifacts');
set('artifact_excludes_file', __DIR__ . '/../../config/artifact.excludes');

set('artifact_path', function () {
    if (!test('[ -d {{artifact_dir}} ]')) {
        run('mkdir {{artifact_dir}}');
    }
    return get('artifact_dir') . '/' . get('artifact_file');
});

task('artifact:package', function () {
    run('tar --exclude-from={{artifact_excludes_file}} -czf {{artifact_path}} .');
});

task('artifact:upload', function () {
    upload(get('artifact_path'), '{{release_path}}');
});

task('artifact:extract', function () {
    run(
        'tar -xzpf {{release_path}}/{{artifact_file}} -C {{release_path}};'
        . 'rm -rf {{release_path}}/{{artifact_file}}'
    );
});
