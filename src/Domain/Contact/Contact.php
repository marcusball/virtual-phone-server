<?php 
namespace VirtualPhone\Domain\Contact;

use VirtualPhone\PhoneNumber\PhoneNumber;

class Contact {

    /** @var int */
    public $id;

    /** @var int */
    protected $personId;

    /** @var string */
    public $name;

    /** @var PhoneNumber */
    public $phone;

    /** @var string */
    public $description;

    /** @var string */
    public $createdAt;

    /** @var string */
    public $updatedAt;

    public function getPersonId(): int {
        return $this->personId;
    }

    public function setPersonId($personId): void {
        $this->personId = $personId;
    }
}