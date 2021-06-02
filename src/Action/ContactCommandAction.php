<?php
namespace VirtualPhone\Action;

use VirtualPhone\Domain\Contact\Service\ContactCommandService;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use VirtualPhone\API\APIResponse;
use VirtualPhone\Domain\Contact\Data\CreateContactCommandData;
use VirtualPhone\Domain\PhoneNumber\Service\PhoneNumberQueryService;
use VirtualPhone\Exception\ValidationException;

final class ContactCommandAction {

    /** @var ContactCommandService */
    private $service; 

    /** @var PhoneNumberQueryService */
    private $phoneService;

    public function __construct(
        ContactCommandService $service,
        PhoneNumberQueryService $phoneService
    ) {
        $this->service = $service;
        $this->phoneService = $phoneService;
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        $body = $request->getParsedBody();

        $personId    = $request->getAttribute('personId');
        $phoneId     = $body['phoneId'];
        $name        = $body['name'];
        $description = $body['description'];

        $phone = $this->phoneService->getById($phoneId);

        if (!$phone) {
            return APIResponse::error($response, 'Phone number not found', 404)->into();
        }

        $contactData = new CreateContactCommandData;
        $contactData->personId    = $personId;
        $contactData->name        = $name;
        $contactData->phoneId     = $phone->id;
        $contactData->description = $description;

        try {
            $contactId = $this->service->createContact($contactData);
        }
        catch (ValidationException $e) {
            return APIResponse::error($response, $e->getMessage(), 400)->into();
        }

        return APIResponse::success($response, ['id' => $contactId], 'contact', 201)->into();
    }
}