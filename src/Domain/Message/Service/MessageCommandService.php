<?php 
namespace VirtualPhone\Domain\Message\Service;

use Monolog\Logger;
use VirtualPhone\Domain\Contact\Contact;
use VirtualPhone\Domain\Message\Data\CreateOutboundMessageCommandData;
use VirtualPhone\Domain\Message\Repository\MessageCommandRepository;
use VirtualPhone\Exception\ValidationException;

final class MessageCommandService {

    /** @var MessageCommandRepository */
    private $repository; 

    /** @var Logger */
    private $logger;

    public function __construct(MessageCommandRepository $repository, Logger $logger) {
        $this->repository = $repository;
        $this->logger = $logger;
    }

    public function create(int $personId, Contact $contact, string $body, string $from): int {
        if ($personId != $contact->getPersonId()) {
            $this->logger->error("Person $personId attempted to send a message to Contact {$contact->id} owned by Person {$contact->getPersonId()}!");
            throw new ValidationException('Invalid Contact!');
        }

        $data = new CreateOutboundMessageCommandData;

        $data->personId = $personId;
        $data->contactId = $contact->id;
        $data->to = $contact->phone->phoneNumber;
        $data->from = $from;
        $data->body = $body;

        return $this->repository->create($data);
    }
}