<?php

use Slim\App;

return function (App $app) {
    $app->get('/', \VirtualPhone\Action\HomeAction::class)->setName('home');
    $app->get('/test/{foo}', \VirtualPhone\Action\TestReadAction::class)->setName('test-get');
    //$app->post('/users', \App\Action\UserCreateAction::class)->setName('users-post');
    $app->post('/phone', [\VirtualPhone\Action\PhoneNumberAction::class, 'create']);
};