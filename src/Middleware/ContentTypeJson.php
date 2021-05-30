<?php
namespace VirtualPhone\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ContentTypeJson {

    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        $request = $handler->handle($request);

        if (empty($request->getHeader('Content-Type'))) {
            $request = $request->withHeader('Content-Type', 'application/json');
        }

        return $request;
    }
}