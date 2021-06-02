<?php 
namespace VirtualPhone\Domain\Thread\Repository;

use PDO;
use VirtualPhone\Domain\Thread\Data\ThreadQueryData;

final class ThreadQueryRepository {

    /** @var PDO */
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Get all message threads for a specific Person.
     *
     * @param integer $personId
     * @return ThreadQueryData[]|false
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
                created_at AS "createdAt",
                updated_at AS "updatedAt"
            FROM thread
            WHERE rank = 1
            ORDER BY created_at DESC
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':pid' => $personId,
        ]);

        $stmt->setFetchMode(PDO::FETCH_CLASS, ThreadQueryData::class);

        return $stmt->fetchAll();
    }
}