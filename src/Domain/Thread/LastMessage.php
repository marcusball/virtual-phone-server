<?php 
namespace VirtualPhone\Domain\Thread;

use VirtualPhone\Domain\Contact\Contact;

class LastMessage {
    /** @var int */
    public $id;

    /** @var string */
    public $body;

    /** @var string 'inbound' or 'outbound' */
    public $direction;

    /** @var string */
    public $createdAt;
}