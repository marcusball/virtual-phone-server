<?php 
namespace VirtualPhone\Domain\Contact\Service;

use VirtualPhone\Domain\Contact\Contact;

/**
 * Service for when many repeat calls to `getById` will be made.
 * This will query all contacts and store them in an indexed array for faster lookups.
 */
class RepeatContactQueryService {

    /** @var ContactQueryService */
    private $queryService;

    /** @var array<int,array<int, Contact[]>> */
    private $contactsLookup = [];

    public function __construct(ContactQueryService $queryService) {
        $this->queryService = $queryService;
    }

    public function getById(int $id, int $personId): Contact|null {
        // If we have not yet queried contacts for this person, query now. 
        if (!isset($this->contactsLookup[$personId])) {
            // Get all contacts 
            $contacts = $this->queryService->getAllForPerson($personId);

            // Reformat into indexed array for easier lookup.
            $this->contactsLookup[$personId] = $this->buildContactLookup($contacts);
        }

        // Get the contact by ID if it exists. 
        return $this->contactsLookup[$personId][$id] ?? null;
    }

    /**
     * Build a lookup table for $contacts; creates an associative array for contact $id to Contact object.
     *
     * @param Contacts[] $contacts
     * @return array<int, Contact>
     */
    private function buildContactLookup(array $contacts): array {
        $lookup = [];

        foreach ($contacts as $id => $contact) {
            $lookup[$id] = $contact;
        }

        return $lookup;
    }
}