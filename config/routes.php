<?php

use Slim\App;

return function (App $app) {
    $app->get('/', \App\Action\HomeAction::class)->setName('home');
    $app->get('/test/{foo}', \App\Action\TestReadAction::class)->setName('test-get');
    //$app->post('/users', \App\Action\UserCreateAction::class)->setName('users-post');
};