<?php 
namespace VirtualPhone\Action;

use VirtualPhone\Domain\PhoneNumber\Service\PhoneNumberCommandService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use VirtualPhone\API\APIResponse;

final class PhoneNumberCommandAction {
    private $phoneNumberService;

    public function __construct(PhoneNumberCommandService $service) {
        $this->phoneNumberService = $service;
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        // Collect input from the HTTP request
        $data = (array)$request->getParsedBody();

        $phoneId = $this->phoneNumberService->createPhoneNumber($data['phone_number']);

        return APIResponse::success($response, ['id' => $phoneId], 'phone_number', 201)->into();
    }
}