<?php 
namespace VirtualPhone\Domain\Thread\Repository;

use PDO;
use VirtualPhone\Domain\Message\Service\MessageQueryService;
use VirtualPhone\Domain\Thread\Data\ThreadQueryData;
use VirtualPhone\Domain\Thread\Data\ThreadsQueryData;

final class ThreadQueryRepository {

    /** @var PDO */
    private $db;

    /** @var MessageQueryService */
    private $messageService;

    public function __construct(PDO $db, MessageQueryService $messageService) {
        $this->db = $db;
        $this->messageService = $messageService;
    }

    /**
     * Get all message threads for a specific Person.
     *
     * @param integer $personId
     * @return ThreadsQueryData[]|false
     */
    public function getThreadsForPerson(int $personId): array|bool {
        $sql = 
            'WITH thread AS (
                SELECT 
                    message.*,
                    RANK() OVER (
                        PARTITION BY contact_id
                        ORDER BY created_at DESC
                    )
                FROM message
                WHERE person_id = :pid
            )
            SELECT 
                id,
                sid,
                person_id  AS "personId",
                contact_id AS "contactId",
                body,
                direction,
                status,
                UTCFMT(created_at) AS "createdAt",
                UTCFMT(updated_at) AS "updatedAt"
            FROM thread
            WHERE rank = 1
            ORDER BY created_at DESC
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':pid' => $personId,
        ]);

        $stmt->setFetchMode(PDO::FETCH_CLASS, ThreadsQueryData::class);

        return $stmt->fetchAll();
    }

    /**
     * Get a thread, and all associated messages
     *
     * @param integer $threadId The ID of the Thread (currently the Contact ID).
     * @param integer $personId
     * @return ThreadQueryData|false
     */
    public function getThread(int $threadId, int $personId): ThreadQueryData|bool {
        $messages = $this->messageService->getAllByContact($threadId, $personId);

        if (!$messages) {
            return false;
        }

        $thread = new ThreadQueryData;
        $thread->id = $threadId;
        $thread->contactId = $threadId;
        $thread->personId = $personId;
        $thread->messages = $messages;

        return $thread;
    }
}