<?php 
namespace VirtualPhone\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use VirtualPhone\API\APIResponse;
use VirtualPhone\Domain\Contact\Service\ContactQueryService;

class ContactQueryAction {

    private $service;

    public function __construct(ContactQueryService $service) {
        $this->service = $service;
    }

    public function getAll(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        $personId = $request->getAttribute('personId');

        $contacts = $this->service->getAllForPerson($personId) ?? [];

        return APIResponse::success($response, $contacts, 'contacts')->into();
    }
}