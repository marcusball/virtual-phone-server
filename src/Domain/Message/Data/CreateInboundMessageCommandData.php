<?php 
namespace VirtualPhone\Domain\Message\Data; 

class CreateInboundMessageCommandData {
    /** @var string The unique Twilio ID of this message. */
    public $sid;

    /** @var int The ID of the contact from which this message was sent. */
    public $contactId; 

    /** @var int The ID of the person receiving the message. */
    public $personId; 

    /** @var string The message body. */
    public $body; 

    /** @var string|null The current delivery status of this message. */
    public $status;
}