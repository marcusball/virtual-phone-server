<?php 
namespace VirtualPhone\Domain\Person\Service;

use VirtualPhone\Domain\Person\Data\PersonData;
use VirtualPhone\Domain\Person\Repository\PersonQueryRepository;

final class PersonQueryService {

    /** @var PersonQueryRepository */
    private $repository;

    public function __construct(PersonQueryRepository $repository) {
        $this->repository = $repository;
    }

    public function getByPhoneId(int $phoneId): PersonData {
        $person = $this->repository->getByPhoneId($phoneId);

        if (!$person) {
            throw new \Exception('Failed to retreive person corresponding to phone number!');
        }

        return $person;
    }
}