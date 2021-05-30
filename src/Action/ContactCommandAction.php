<?php
namespace VirtualPhone\Action;

use VirtualPhone\Domain\Contact\Service\ContactCommandService;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

final class ContactCommandAction {

    private $service; 

    public function __construct(ContactCommandService $service) {
        $this->service = $service;
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        $contactData = $request->getParsedBody();

        $contactId = $this->service->createContact($contactData);

        $response->getBody()->write(json_encode([
            'contact_id' => $contactId,
        ]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(201);
    }
}