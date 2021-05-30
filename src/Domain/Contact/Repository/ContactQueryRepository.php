<?php 
namespace VirtualPhone\Domain\Contact\Repository;

use PDO;
use VirtualPhone\Domain\Contact\Data\ContactData;

class ContactQueryRepository {

    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getById(int $id, int $personId): ContactData|bool {
        $sql = 
            'SELECT
                id,
                person_id AS "personId",
                name,
                phone_id  AS "phoneId",
                description,
                created_at AS "createdAt",
                updated_at AS "updatedAt"
            FROM contact
            WHERE 
                id = :id AND
                person_id = :pid
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':pid' => $personId,
        ]);

        $stmt->setFetchMode(PDO::FETCH_CLASS, ContactData::class);

        return $stmt->fetch();
    }
}