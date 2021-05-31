<?php 
namespace VirtualPhone\Domain\PhoneNumber\Repository;

use PDO;

class PhoneNumberCommandRepository {

    /** @var PDO */
    private $db;

    /**
     * Constructor
     *
     * @param PDO $db
     */
    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function insertPhoneNumber(string $phoneNumber): int {
        $sql = 
            'INSERT INTO phone_number (phone_number)
            VALUES (:num)
            RETURNING id
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':num' => $phoneNumber,
        ]);
        $stmt->setFetchMode(PDO::FETCH_COLUMN, 0);
        $id = $stmt->fetch();

        return $id;
    }
}