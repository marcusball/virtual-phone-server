<?php 
namespace VirtualPhone\Domain\Contact\Data; 

final class ContactData {

    /** @var int */
    public $id;

    /** @var int */
    public $personId; 

    /** @var string */
    public $name;

    /** @var int */
    public $phoneId; 

    /** @var string */
    public $description;

    /** @var string */
    public $createdAt;

    /** @var string */
    public $updatedAt;
}