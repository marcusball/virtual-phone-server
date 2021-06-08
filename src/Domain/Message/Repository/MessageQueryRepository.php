<?php 
namespace VirtualPhone\Domain\Message\Repository;

use PDO;
use VirtualPhone\Domain\Message\Data\MessageQueryData;

class MessageQueryRepository {

    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Get all Messages between a person and a specific contact.
     *
     * @param integer $contactId
     * @param integer $personId
     * @return MessageQueryData[]|false
     */
    public function getAllByContact(int $contactId, int $personId): array|bool {
        $sql = 
            'SELECT 
                id,
                sid, 
                person_id AS "personId",
                contact_id AS "contactId",
                body,
                direction,
                status,
                UTCFMT(created_at) AS "createdAt",
                UTCFMT(updated_at) AS "updatedAt"
            FROM message
            WHERE 
                contact_id = :cid AND
                person_id  = :pid
            ORDER BY created_at DESC
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':cid' => $contactId,
            ':pid' => $personId,
        ]);

        $stmt->setFetchMode(PDO::FETCH_CLASS, MessageQueryData::class);

        return $stmt->fetchAll();
    }
}