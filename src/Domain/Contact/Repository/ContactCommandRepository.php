<?php
namespace VirtualPhone\Domain\Contact\Repository;

use PDO;
use VirtualPhone\Domain\Contact\Data\CreateContactCommandData;

class ContactCommandRepository {

    /** @var PDO */
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function createContact(CreateContactCommandData $data): int {
        $sql = 
            'INSERT INTO contact (
                person_id, name, phone_id, description
            ) 
            VALUES (:pid, :name, :nid, :desc)
            RETURNING id
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':pid'  => $data->personId,
            ':name' => $data->name,
            ':nid'  => $data->phoneId,
            ':desc' => $data->description,
        ]);

        $stmt->setFetchMode(PDO::FETCH_COLUMN, 0);
        return $stmt->fetch();
    }
}