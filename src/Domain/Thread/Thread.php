<?php 
namespace VirtualPhone\Domain\Thread;

use VirtualPhone\Domain\Contact\Contact;
use LastMessage;

class Thread {
    /** @var int */
    public $id;

    /** @var Contact */
    public $contact;

    /** @var LastMessage */
    public $lastMessage;
}