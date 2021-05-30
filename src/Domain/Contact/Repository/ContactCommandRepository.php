<?php
namespace VirtualPhone\Domain\Contact\Repository;

use PDO;

class ContactCommandRepository {

    /** @var PDO */
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function createContact(array $data): int {
        $sql = 
            'INSERT INTO contact (
                person_id, name, phone_id, description
            ) 
            VALUES (:pid, :name, :nid, :desc)
            RETURNING id
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':pid'  => $data['person_id'],
            ':name' => $data['name'],
            ':nid'  => $data['phone_id'],
            ':desc' => $data['description'],
        ]);

        $stmt->setFetchMode(PDO::FETCH_COLUMN, 0);
        return $stmt->fetch();
    }
}