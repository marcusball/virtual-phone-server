<?php 
namespace VirtualPhone\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use VirtualPhone\API\APIResponse;
use VirtualPhone\Domain\Contact\Repository\ContactQueryRepository;
use VirtualPhone\Domain\Contact\Service\ContactQueryService;
use VirtualPhone\Domain\Message\Service\MessageCommandService;
use VirtualPhone\Domain\PhoneNumber\Service\PhoneNumberQueryService;

class MessageCreateAction {

    /** @var MessageCommandService */
    private $messageService;

    /** @var ContactQueryService */
    private $contactService;

    public function __construct(MessageCommandService $messageService, ContactQueryService $contactService) {
        $this->messageService = $messageService;
        $this->contactService = $contactService;
    }

    /**
     * Invoke.
     *
     * @param ServerRequestInterface $request The request
     * @param ResponseInterface $response The response
     *
     * @return ResponseInterface The response
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        
        $contactId = $args['contactId'];

        $data = (object)$request->getParsedBody();

        $personId = $request->getAttribute('personId');
        $body     = $data->body;
        $from     = $data->from;

        $contact = $this->contactService->getById($contactId, $personId);

        if ($contact === null) {
            return APIResponse::error($response, 'Not found', 404)->into();
        }

        $messageId = $this->messageService->create($personId, $contact, $body, $from);

        return APIResponse::success($response, ['id' => $messageId], 'message', 201)->into();
    }
}