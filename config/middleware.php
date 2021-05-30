<?php

use Slim\App;
use Slim\Middleware\ErrorMiddleware;
use VirtualPhone\Middleware\ContentTypeJson;

return function (App $app) {
    // Parse json, form data and xml
    $app->addBodyParsingMiddleware();

    // Add the Slim built-in routing middleware
    $app->addRoutingMiddleware();

    // Catch exceptions and errors
    $app->add(ErrorMiddleware::class);

    // Always return Content-Type as JSON
    $app->add(ContentTypeJson::class);
};