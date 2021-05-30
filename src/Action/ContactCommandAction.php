<?php
namespace VirtualPhone\Action;

use VirtualPhone\Domain\Contact\Service\ContactCommandService;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
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
            $response->getBody()->write(json_encode([
                'error' => $e->getMessage()
            ]));

            return $response->withStatus(400);
        }

        $response->getBody()->write(json_encode([
            'contact_id' => $contactId,
        ]));

        return $response->withStatus(201);
    }
}