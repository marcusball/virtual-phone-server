<?php 
namespace VirtualPhone\Domain\Message\Service;

use VirtualPhone\Domain\Message\Data\MessageQueryData;
use VirtualPhone\Domain\Message\Message;
use VirtualPhone\Domain\Message\Repository\MessageQueryRepository;

class MessageQueryService {

    /** @var MessageQueryRepository */
    private $repository;

    public function __construct(MessageQueryRepository $repository) {
        $this->repository = $repository;
    }

    public function getAllByContact(int $contactId, int $personId): array|null {
        $messages = $this->repository->getAllByContact($contactId, $personId);

        if ($messages === false) {
            return null;
        }

        return array_map([$this, 'convertToMessage'], $messages);
    }

    private function convertToMessage(MessageQueryData $data): Message {
        $message = new Message;
        $message->id        = $data->id;
        $message->sid       = $data->sid;
        $message->body      = $data->body;
        $message->direction = $data->direction;
        $message->status    = $data->status;
        $message->createdAt = $data->createdAt;
        $message->updatedAt = $data->updatedAt;
        $message->setContactId($data->contactId);
        $message->setPersonId($data->personId);

        return $message;
    }
}