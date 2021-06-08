<?php 
namespace VirtualPhone\Domain\Thread\Service;

use VirtualPhone\Domain\Contact\Service\RepeatContactQueryService;
use VirtualPhone\Domain\Thread\Data\ThreadQueryData;
use VirtualPhone\Domain\Thread\Repository\ThreadQueryRepository;
use VirtualPhone\Domain\Thread\Thread;
use VirtualPhone\Domain\Thread\LastMessage;

class ThreadQueryService {

    /** @var ThreadQueryRepository */
    private $threadRepository;

    /** @var RepeatContactQueryService */
    private $contactQueryService;

    public function __construct(
        ThreadQueryRepository $threadRepository,
        RepeatContactQueryService $contactQueryService
    ) {
        $this->threadRepository = $threadRepository;
        $this->contactQueryService = $contactQueryService;
    }

    public function getAllForPerson(int $personId): array|null {
        $threads = $this->threadRepository->getThreadsForPerson($personId);

        if (!$threads) {
            return null;
        }

        return array_map([$this, 'convertToThread'], $threads);
    }

    private function convertToThread(ThreadQueryData $data): Thread {
        $contact = $this->contactQueryService->getById($data->contactId, $data->personId);

        $thread = new Thread;
        $thread->id = $contact->id ?? null;
        $thread->contact = $contact;
        $thread->lastMessage = new LastMessage;
        $thread->lastMessage->id        = $data->id;
        $thread->lastMessage->body      = $data->body;
        $thread->lastMessage->direction = $data->direction;
        $thread->lastMessage->createdAt = $data->createdAt;

        return $thread;
    }
}