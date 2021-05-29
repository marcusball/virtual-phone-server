<?php

use Slim\App;
use VirtualPhone\Action\PhoneNumberQueryAction;

return function (App $app) {
    $app->get('/', \VirtualPhone\Action\HomeAction::class)->setName('home');
    $app->get('/test/{foo}', \VirtualPhone\Action\TestReadAction::class)->setName('test-get');
    //$app->post('/users', \App\Action\UserCreateAction::class)->setName('users-post');
    $app->get('/phone/{id}', [PhoneNumberQueryAction::class, 'get']);
    $app->post('/phone', [\VirtualPhone\Action\PhoneNumberCommandAction::class, 'create']);
};