<?php
namespace VirtualPhone\Action\Auth;

use VirtualPhone\API\JwtAuth;
use JsonException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use VirtualPhone\API\APIResponse;

final class TokenCreateAction {

    private JwtAuth $jwtAuth;

    /**
     * The constructor.
     *
     * @param JwtAuth $jwtAuth The JWT auth
     */
    public function __construct(JwtAuth $jwtAuth)
    {
        $this->jwtAuth = $jwtAuth;
    }

    /**
     * Invoke.
     *
     * @param ServerRequestInterface $request The request
     * @param ResponseInterface $response The response
     *
     * @throws JsonException
     *
     * @return ResponseInterface The response
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        $data = (array)$request->getParsedBody();

        $password = (string)($data['password'] ?? '');
        $person_id = (int)$data['person_id'];

        // Validate login (pseudo code)
        // Warning: This should be done in an application service and not here!
        // $userAuthData = $this->userAuth->authenticate($username, $password);
        $isValidLogin = ($password === $_SERVER['TOKEN_PASSWORD']);

        if (!$isValidLogin) {
            // Invalid authentication credentials
            return APIResponse::error($response, ['Unauthorized'], 401)->into();
        }

        // Create a fresh token
        $token = $this->jwtAuth->createJwt([
            'personId' => $person_id,
        ]);

        // Transform the result into a OAuh 2.0 Access Token Response
        // https://www.oauth.com/oauth2-servers/access-tokens/access-token-response/
        $result = [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $this->jwtAuth->getLifetime(),
        ];

        // Build the HTTP response
        return APIResponse::success($response, $result, 'token')->into();
    }
}
