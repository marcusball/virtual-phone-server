<?php 
namespace VirtualPhone\Domain\Person\Repository;

use VirtualPhone\Domain\Person\Data\PersonData;

final class PersonQueryRepository {

    public function getByPhoneId(int $phoneId): PersonData|null {
        $data = new PersonData;
        $data->id = 1; 
        return $data;
    }
}