<?php
namespace VirtualPhone\Domain\Contact\Service;

use VirtualPhone\Domain\Contact\Contact;
use VirtualPhone\Domain\Contact\Repository\ContactQueryRepository;
use VirtualPhone\Domain\PhoneNumber\Service\PhoneNumberQueryService;

final class ContactQueryService {

    /** @var ContactQueryRepository */
    private $repository;

    /** @var PhoneNumberQueryService */
    private $phoneService;

    public function __construct(ContactQueryRepository $repository, PhoneNumberQueryService $phoneService) {
        $this->repository = $repository;
        $this->phoneService = $phoneService;
    }

    public function getById(int $id, int $personId): Contact|null {
        $contactData = $this->repository->getById($id, $personId);

        if ($contactData === false) {
            return null;
        }

        $contact = new Contact;
        $contact->setPersonId($contactData->personId);
        $contact->id          = $contactData->id;
        $contact->name        = $contactData->name;
        $contact->description = $contactData->description;
        $contact->createdAt   = $contactData->createdAt;
        $contact->updatedAt   = $contactData->updatedAt;
        $contact->phone       = null;

        if ($contactData->phoneId !== null) {
            $contact->phone = $this->phoneService->getById($contactData->phoneId);
        }

        return $contact;
    }
}