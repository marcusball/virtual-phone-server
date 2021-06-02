<?php
namespace VirtualPhone\Domain\Contact\Service;

use VirtualPhone\Domain\Contact\Data\CreateContactCommandData;
use VirtualPhone\Domain\Contact\Repository\ContactCommandRepository;
use VirtualPhone\Exception\ValidationException;

final class ContactCommandService {

    private $repository; 

    public function __construct(ContactCommandRepository $repository) {
        $this->repository = $repository;
    }

    /**
     * Create a new Contact
     *
     * @param array $data
     * @return integer The new Contact ID.
     * @throws ValidationException
     * @throws \PDOException
     */
    public function createContact(CreateContactCommandData $data): int {
        try {
            return $this->repository->createContact($data);
        }
        catch (\PDOException $e) {
            // If unique violating unique constraint
            if ($e->getCode() == 23505) {
                throw new ValidationException('A contact for this number already exists!');
            }

            // Otherwise bubble up the original exception.
            throw $e;
        }
    }
}