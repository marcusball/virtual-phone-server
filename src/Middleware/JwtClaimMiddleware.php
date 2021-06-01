<?php
namespace VirtualPhone\Middleware;

use VirtualPhone\API\JwtAuth;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * JWT Claim middleware.
 */
final class JwtClaimMiddleware implements MiddlewareInterface {

    /**
     * @var JwtAuth
     */
    private JwtAuth $jwtAuth;

    /**
     * The constructor.
     *
     * @param JwtAuth $jwtAuth The JWT auth
     */
    public function __construct(JwtAuth $jwtAuth) {
        $this->jwtAuth = $jwtAuth;
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
        $authorization = explode(' ', (string)$request->getHeaderLine('Authorization'));

        $type = $authorization[0] ?? '';
        $credentials = $authorization[1] ?? '';

        // Does anyone else think it's very silly that the OAuth 2 spec requires this to be capitalized? 
        if ($type !== 'Bearer') {
            return $handler->handle($request);
        }

        $token = $this->jwtAuth->validateToken($credentials);
        if ($token) {
            // Append valid token
            $request = $request->withAttribute('token', $token);
            // Append the user id as request attribute
            $request = $request->withAttribute('uid', $token->claims()->get('uid'));
        }

        return $handler->handle($request);
    }
}
