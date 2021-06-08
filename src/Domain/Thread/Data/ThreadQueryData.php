<?php 
namespace VirtualPhone\Domain\Thread\Data;

use VirtualPhone\Domain\Message\Message;
final class ThreadQueryData {

    /** @var int */
    public $id;

    /** @var int */
    public $personId;

    /** @var int */
    public $contactId;

    /** @var Message[] */
    public $messages;
}