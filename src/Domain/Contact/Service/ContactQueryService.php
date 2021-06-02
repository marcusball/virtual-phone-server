<?php
namespace VirtualPhone\Domain\Contact\Service;

use VirtualPhone\Domain\Contact\Contact;
use VirtualPhone\Domain\Contact\Data\ContactData;
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

        return $this->convertToContact($contactData);
    }

    public function getByPhoneId(int $phoneId, int $personId): Contact|null {
        $contactData = $this->repository->getByPhoneId($phoneId, $personId);

        if ($contactData === false) {
            return null;
        }

        return $this->convertToContact($contactData);
    }

    /**
     * Get all contacts for a specific person.
     *
     * @param integer $personId
     * @return Contact[]|null
     */
    public function getAllForPerson(int $personId): array|null {
        $contactsData = $this->repository->getAllForPerson($personId);

        if ($contactsData === false){ 
            return null;
        }

        return array_map([$this, 'convertToContact'], $contactsData);
    }

    private function convertToContact(ContactData $data): Contact {
        $contact = new Contact;
        $contact->setPersonId(  $data->personId);
        $contact->id          = $data->id;
        $contact->name        = $data->name;
        $contact->description = $data->description;
        $contact->createdAt   = $data->createdAt;
        $contact->updatedAt   = $data->updatedAt;
        $contact->phone       = null;

        if ($data->phoneId !== null) {
            $contact->phone = $this->phoneService->getById($data->phoneId);
        }

        return $contact;
    }
}