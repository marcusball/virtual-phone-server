<?php 
namespace VirtualPhone\Domain\PhoneNumber;

class PhoneNumber {
    /** @var int */
    public $id; 

    /** @var string The full phone number, in E.123 international notation. */
    public $phoneNumber;

    /** @var string */
    public $createdAt;

    /** @var string */
    public $updatedAt;
}