<?php 
namespace VirtualPhone\Action;

use VirtualPhone\Domain\PhoneNumber\Service\PhoneNumberQueryService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class PhoneNumberQueryAction {
    private $phoneNumberService;

    public function __construct(PhoneNumberQueryService $service) {
        $this->phoneNumberService = $service;
    }

    public function get(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        $phoneId = (int)$args['id'];

        $phoneData = $this->phoneNumberService->getById($phoneId);

        if ($phoneData === false) {
            return $response
                ->withStatus(404);
        }

        $response
            ->getBody()
            ->write(json_encode([
                'phone_number' => $phoneData
            ]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}