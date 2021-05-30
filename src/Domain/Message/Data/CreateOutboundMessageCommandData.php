<?php 
namespace VirtualPhone\Domain\Message\Data; 

class CreateOutboundMessageCommandData {
    /** @var string The phone number to which the message will be sent. */
    public $to;

    /** @var int The ID of the contact to which the message will be sent. */
    public $contactId; 

    /** @var string The phone number from which to send the message. */
    public $from; 

    /** @var int The ID of the person sending the message. */
    public $personId; 

    /** @var string The message body. */
    public $body; 
}