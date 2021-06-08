<?php 
namespace VirtualPhone\Domain\Thread\Data;

final class ThreadsQueryData {

    /** @var int */
    public $id;
    
    /** @var string */
    public $sid;

    /** @var int */
    public $personId;

    /** @var int */
    public $contactId;

    /** @var string */
    public $body;

    /** @var string 'inbound' or 'outbound' */
    public $direction;

    /** @var string  */
    public $status;

    /** @var string */
    public $createdAt;

    /** @var string */
    public $updatedAt;
}