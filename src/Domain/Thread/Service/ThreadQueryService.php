<?php 
namespace VirtualPhone\Domain\Thread\Service;

use VirtualPhone\Domain\Contact\Service\RepeatContactQueryService;
use VirtualPhone\Domain\Thread\Data\ThreadsQueryData;
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

    /**
     * Get a thread, and all associated messages
     *
     * @param integer $threadId The ID of the Thread (currently the Contact ID).
     * @param integer $personId
     * @return Thread|null
     */
    public function getById(int $threadId, int $personId): Thread|null {
        $data = $this->threadRepository->getThread($threadId, $personId);

        if (!$data) {
            return null;
        }

        $contact = $this->contactQueryService->getById($data->contactId, $data->personId);

        $thread = new Thread;
        $thread->id = $data->id;
        $thread->contact = $contact;
        $thread->messages = $data->messages;        

        if (!empty($thread->messages)) {
            $thread->lastMessage = new LastMessage;
            $thread->lastMessage->id        = $thread->messages[0]->id;
            $thread->lastMessage->body      = $thread->messages[0]->body;
            $thread->lastMessage->direction = $thread->messages[0]->direction;
            $thread->lastMessage->createdAt = $thread->messages[0]->createdAt;
        }

        return $thread;
    }

    private function convertToThread(ThreadsQueryData $data): Thread {
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