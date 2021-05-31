<?php 
namespace VirtualPhone\Domain\Message\Service;

use Monolog\Logger;
use VirtualPhone\Domain\Contact\Contact;
use VirtualPhone\Domain\Message\Data\CreateInboundMessageCommandData;
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

    public function receive(string $sid, int $personId, Contact $contact, string $body, string $status): int {
        if ($personId != $contact->getPersonId()) {
            $this->logger->error("Person $personId receiving a message from Contact {$contact->id} owned by Person {$contact->getPersonId()}!");
            throw new ValidationException('Invalid Contact!');
        }

        $data = new CreateInboundMessageCommandData;
        $data->sid       = $sid;
        $data->personId  = $personId;
        $data->contactId = $contact->id;
        $data->body      = $body;
        $data->status    = $status;

        return $this->repository->receive($data);
    }

    public function updateStatus (int $messageId, string $status) {
        try {
            $this->repository->updateStatus($messageId, $status);
        }
        catch (\PDOException $e) {
            // If we receive this error code, it's probably from a status which is not in the expected enum of known status values. 
            if ($e->getCode() == '22P02') {
                $this->logger->warning('Received invalid message status "' . $status . '".');

                throw new ValidationException('Invalid message status "' . $status . '"!');
            }

            // Otherwise, it's some other unhandled PDO exception, just bubble it up. 
            throw $e;
        }
    }
}