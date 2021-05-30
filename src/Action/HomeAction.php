<?php

namespace VirtualPhone\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteParser;
use VirtualPhone\API\APIResponse;

/**
 * Action
 */
final class HomeAction {

    private $p;

    public function __construct(RouteParser $parser)
    {
        $this->p =$parser;
    }

    /**
     * Invoke.
     *
     * @param ServerRequestInterface $request The request
     * @param ResponseInterface $response The response
     *
     * @return ResponseInterface The response
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        return APIResponse::success($response, 'Hello world!')
            ->withMessage($this->p->urlFor('message-status-webhook', ['messageId' => 123]))    
            ->withMessage($this->p->fullUrlFor($request->getUri(), 'message-status-webhook', ['messageId' => 123]))    
            ->withMessage($this->p->relativeUrlFor('message-status-webhook', ['messageId' => 123]))    
            ->into();
    }
}