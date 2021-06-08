<?php 
namespace VirtualPhone\Domain\Message;

final class Message {

    /** @var int */
    public $id;

    /** @var string */
    public $sid;

    /** @var int */
    private $personId;

    /** @var int */
    private $contactId;

    /** @var string */
    public $body;

    /** @var string "inbound" or "outbound" */
    public $direction;

    /** @var string */
    public $status;

    /** @var string */
    public $createdAt;

    /** @var string */
    public $updatedAt;

    public function getPersonId(): int {
        return $this->personId;
    }

    public function setPersonId(int $id) {
        $this->personId = $id;
    }

    public function getContactId(): int {
        return $this->contactId;
    }

    public function setContactId(int $id) {
        $this->contactId = $id;
    }
}