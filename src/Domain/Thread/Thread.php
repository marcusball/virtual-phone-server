<?php 
namespace VirtualPhone\Domain\Thread;

use VirtualPhone\Domain\Contact\Contact;

class Thread {
    /** @var Contact */
    public $contact;

    /** @var string */
    public $body;

    /** @var string 'inbound' or 'outbound' */
    public $direction;

    /** @var string */
    public $lastMessageAt;
}