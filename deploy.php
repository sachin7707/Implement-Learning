<?php
namespace Deployer;

require 'recipe/common.php';
require 'recipe/laravel.php';
//require 'vendor/konform/recipes/recipe/konform.php';

// Project name
set('application', 'ilibackend');

// Project repository
set('repository', 'ssh://git@git.konform.com:7999/ili/ili-backend.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true); 

// Hosts
host('next')
    ->hostname('dev.konform.com')
    ->stage('develop')
    ->roles(['db','web'])
    ->set('branch', 'next')
    ->set('deploy_path', '/domains/konform/sites/{{application}}');

// default stage host is selected as next
set('default_stage', 'next');

desc('Add symlink for public -> web, in the current release');
task('deploy:link_web', function () {
    // Symlink shared dir to release dir
    run('{{bin/symlink}} {{release_path}}/public {{release_path}}/web');
});

before('deploy:symlink', 'deploy:link_web');

desc('Restart supervisord, to reload the queues');
task('reload:supervisord', function () {
    // Symlink shared dir to release dir
    run('sudo systemctl restart supervisord', ['tty' => true]);
});

after('deploy:unlock', 'reload:supervisord');

task('deploy', [
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:vendors',
    'deploy:writable',
    'artisan:cache:clear',
    'artisan:optimize',
    'artisan:migrate',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
]);

// [Optional] If deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');