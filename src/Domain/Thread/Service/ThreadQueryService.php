<?php 
namespace VirtualPhone\Domain\Thread\Service;

use VirtualPhone\Domain\Contact\Service\RepeatContactQueryService;
use VirtualPhone\Domain\Thread\Data\ThreadQueryData;
use VirtualPhone\Domain\Thread\Repository\ThreadQueryRepository;
use VirtualPhone\Domain\Thread\Thread;

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
        $thread = new Thread;
        $thread->contact = $this->contactQueryService->getById($data->contactId, $data->personId);
        $thread->body = $data->body;
        $thread->direction = $data->direction;
        $thread->lastMessageAt = $data->createdAt;

        return $thread;
    }
}