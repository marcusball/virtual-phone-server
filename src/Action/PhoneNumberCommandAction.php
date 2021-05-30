<?php 
namespace VirtualPhone\Action;

use VirtualPhone\Domain\PhoneNumber\Service\PhoneNumberCommandService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class PhoneNumberCommandAction {
    private $phoneNumberService;

    public function __construct(PhoneNumberCommandService $service) {
        $this->phoneNumberService = $service;
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        // Collect input from the HTTP request
        $data = (array)$request->getParsedBody();

        $phoneId = $this->phoneNumberService->createPhoneNumber($data);

        $result = [
            'phone_number_id' => $phoneId,
        ];

        $response->getBody()->write(json_encode($result));

        return $response->withStatus(201);
    }
}