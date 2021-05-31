<?php

use Slim\App;
use VirtualPhone\Action\ContactCommandAction;
use VirtualPhone\Action\MessageCreateAction;
use VirtualPhone\Action\MessageReceiveAction;
use VirtualPhone\Action\MessageStatusUpdateAction;
use VirtualPhone\Action\PhoneNumberQueryAction;

return function (App $app) {
    $app->get('/', \VirtualPhone\Action\HomeAction::class)->setName('home');
    $app->get('/test/{foo}', \VirtualPhone\Action\TestReadAction::class)->setName('test-get');
    //$app->post('/users', \App\Action\UserCreateAction::class)->setName('users-post');
    $app->get('/phone/{id}', [PhoneNumberQueryAction::class, 'get']);
    $app->post('/phone', [\VirtualPhone\Action\PhoneNumberCommandAction::class, 'create']);

    $app->post('/contact', [ContactCommandAction::class, 'create']);
    $app->post('/contact/{contactId}/message', MessageCreateAction::class);

    $app->post('/webhook/message', MessageReceiveAction::class);
    $app->post('/webhook/message/{messageId}/status', MessageStatusUpdateAction::class)->setName('message-status-webhook');
};