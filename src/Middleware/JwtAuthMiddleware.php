<?php
namespace VirtualPhone\Middleware;

use VirtualPhone\API\JwtAuth;
use VirtualPhone\API\APIResponse;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * JWT Auth middleware.
 */
final class JwtAuthMiddleware implements MiddlewareInterface {

    /**
     * @var JwtAuth
     */
    private JwtAuth $jwtAuth;

    /**
     * @var ResponseFactoryInterface
     */
    private ResponseFactoryInterface $responseFactory;

    /**
     * The constructor.
     *
     * @param JwtAuth $jwtAuth The JWT auth
     * @param ResponseFactoryInterface $responseFactory The response factory
     */
    public function __construct(
        JwtAuth $jwtAuth,
        ResponseFactoryInterface $responseFactory
    ) {
        $this->jwtAuth = $jwtAuth;
        $this->responseFactory = $responseFactory;
    }

    /**
     * Invoke middleware.
     *
     * @param ServerRequestInterface $request The request
     * @param RequestHandlerInterface $handler The handler
     *
     * @return ResponseInterface The response
     */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {

        // Get the Authorization header, then split it it in two at the space character
        // between 'bearer' and the token. 
        $token = explode(' ', (string)$request->getHeaderLine('Authorization'))[1] ?? '';

        if (!$token || !$this->jwtAuth->validateToken($token)) {
            $response = $this->responseFactory->createResponse();
            return APIResponse::error($response, 'Unauthorized', 401)->into();
        }

        return $handler->handle($request);
    }
}
