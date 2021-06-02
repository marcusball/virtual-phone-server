<?php
namespace VirtualPhone\Domain\Contact\Data;

final class CreateContactCommandData {

    /** @var int */
    public $personId;

    /** @var int */
    public $phoneId;

    /** @var string|null */
    public $name;

    /** @var string|null */
    public $description;
}