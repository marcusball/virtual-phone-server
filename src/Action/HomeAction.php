<?php

namespace VirtualPhone\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use VirtualPhone\API\APIResponse;

/**
 * Action
 */
final class HomeAction {
    /**
     * Invoke.
     *
     * @param ServerRequestInterface $request The request
     * @param ResponseInterface $response The response
     *
     * @return ResponseInterface The response
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        return APIResponse::success($response, 'Hello world!')->into();
    }
}