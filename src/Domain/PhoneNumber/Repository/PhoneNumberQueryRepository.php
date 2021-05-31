<?php 
namespace VirtualPhone\Domain\PhoneNumber\Repository;

use VirtualPhone\Domain\PhoneNumber\Data\PhoneNumberData;
use PDO;

class PhoneNumberQueryRepository {

    /** @var PDO */
    private $db; 

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Get a Phone Number given its $id.
     *
     * @param integer $id The Phone Number ID.
     * @return PhoneNumberData|bool The Phone Number data, or false if not found.
     */
    public function getById(int $id): PhoneNumberData|bool {
        $sql = 
            'SELECT 
                id,
                phone_number       AS "phoneNumber",
                UTCFMT(created_at) AS "createdAt",
                UTCFMT(updated_at) AS "updatedAt"
            FROM phone_number
            WHERE id = :id
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id' => $id
        ]);

        $stmt->setFetchMode(PDO::FETCH_CLASS, PhoneNumberData::class);

        return $stmt->fetch();
    }

    /**
     * Get a Phone Number record given its full $phoneNumber.
     *
     * @param string $phoneNumber
     * @return PhoneNumberData|bool The Phone Number data, or null if not found.
     */
    public function getByNumber(string $phoneNumber): PhoneNumberData|bool {
        $sql = 
            'SELECT 
                id,
                phone_number       AS "phoneNumber",
                UTCFMT(created_at) AS "createdAt",
                UTCFMT(updated_at) AS "updatedAt"
            FROM phone_number
            WHERE phone_number = :num
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':num' => $phoneNumber,
        ]);

        $stmt->setFetchMode(PDO::FETCH_CLASS, PhoneNumberData::class);

        return $stmt->fetch();
    }
}