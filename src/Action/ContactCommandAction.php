<?php
namespace VirtualPhone\Action;

use VirtualPhone\Domain\Contact\Service\ContactCommandService;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use VirtualPhone\API\APIResponse;
use VirtualPhone\Exception\ValidationException;

final class ContactCommandAction {

    private $commandService; 

    public function __construct(ContactCommandService $service) {
        $this->service = $service;
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        $contactData = $request->getParsedBody();

        try {
            $contactId = $this->service->createContact($contactData);
        }
        catch (ValidationException $e) {
            return APIResponse::error($response, $e->getMessage(), 400)->into();
        }

        return APIResponse::success($response, ['id' => $contactId], 'contact', 201)->into();
    }
}