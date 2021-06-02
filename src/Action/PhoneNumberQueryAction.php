<?php 
namespace VirtualPhone\Action;

use VirtualPhone\Domain\PhoneNumber\Service\PhoneNumberQueryService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use VirtualPhone\API\APIResponse;

final class PhoneNumberQueryAction {
    private $phoneNumberService;

    public function __construct(PhoneNumberQueryService $service) {
        $this->phoneNumberService = $service;
    }

    public function get(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        $phoneId = (int)$args['id'];

        $phoneData = $this->phoneNumberService->getById($phoneId);

        if ($phoneData === false) {
            return APIResponse::error($response, 'Not found', 404)->into();
        }

        return APIResponse::success($response, $phoneData, 'phoneNumber')->into();
    }
}