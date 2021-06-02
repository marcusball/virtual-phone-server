<?php

use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use VirtualPhone\Action\Auth\TokenCreateAction;
use VirtualPhone\Action\ContactCommandAction;
use VirtualPhone\Action\ContactQueryAction;
use VirtualPhone\Action\MessageCreateAction;
use VirtualPhone\Action\MessageReceiveAction;
use VirtualPhone\Action\MessageStatusUpdateAction;
use VirtualPhone\Action\PhoneNumberQueryAction;
use VirtualPhone\Action\ThreadsListAction;
use VirtualPhone\Middleware\JwtAuthMiddleware;

return function (App $app) {
    $app->get('/', \VirtualPhone\Action\HomeAction::class)->setName('home');
    $app->get('/test/{foo}', \VirtualPhone\Action\TestReadAction::class)->setName('test-get');
    //$app->post('/users', \App\Action\UserCreateAction::class)->setName('users-post');

    $app->group('', function (RouteCollectorProxy $group) {
        $group->get('/phone/{id}', [PhoneNumberQueryAction::class, 'get']);
        $group->post('/phone', [\VirtualPhone\Action\PhoneNumberCommandAction::class, 'create']);

        $group->get('/contact', [ContactQueryAction::class, 'getAll']);
        $group->post('/contact', [ContactCommandAction::class, 'create']);

        $group->get('/thread', ThreadsListAction::class);
        $group->post('/thread/{contactId}', MessageCreateAction::class);
    })->add(JwtAuthMiddleware::class);

    $app->post('/webhook/message', MessageReceiveAction::class)->setName('message-received-webhook');
    $app->post('/webhook/message/{messageId}/status', MessageStatusUpdateAction::class)->setName('message-status-webhook');

    $app->post('/token', TokenCreateAction::class);
};