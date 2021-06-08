<?php 
namespace VirtualPhone\Domain\Thread;

use VirtualPhone\Domain\Contact\Contact;
use VirtualPhone\Domain\Message\Message;
use LastMessage;

class Thread {
    /** @var int */
    public $id;

    /** @var Contact */
    public $contact;

    /** @var Message[]|null */
    public $messages = null;

    /** @var LastMessage */
    public $lastMessage;
}