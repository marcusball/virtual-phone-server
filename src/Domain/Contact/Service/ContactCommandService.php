<?php
namespace VirtualPhone\Domain\Contact\Service;

use VirtualPhone\Domain\Contact\Repository\ContactCommandRepository;

final class ContactCommandService {

    private $repository; 

    public function __construct(ContactCommandRepository $repository) {
        $this->repository = $repository;
    }

    public function createContact(array $data): int {
        return $this->repository->createContact($data);
    }
}