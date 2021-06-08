<?php 
namespace VirtualPhone\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use VirtualPhone\API\APIResponse;
use VirtualPhone\Domain\Contact\Service\ContactQueryService;
use VirtualPhone\Domain\Thread\Service\ThreadQueryService;

class ThreadQueryAction {

    private $service;

    private $contactService;

    public function __construct(ThreadQueryService $service, ContactQueryService $contactService) {
        $this->service = $service;
        $this->contactService = $contactService;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        $personId  = $request->getAttribute('personId');
        $contactId = $args['contactId'];

        // Lookup the contact to ensure it exists.
        $contact = $this->contactService->getById($contactId, $personId);

        // If contact is not found, return an error.
        if (!$contact) {
            return APIResponse::error($response, 'Not found', 404)->into();
        }

        $thread = $this->service->getById($contact->id, $personId);

        return APIResponse::success($response, $thread, 'thread')->into();
    }
}