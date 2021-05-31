<?php 
namespace VirtualPhone\Domain\Message\Data; 

class CreateMessageCommandData {
    /** @var string|null The unique Twilio ID of this message, if known. */
    public $sid;

    /** @var int The ID of the contact from whom, or to whom, this message was sent. */
    public $contactId; 

    /** @var int The ID of the person sending or receiving the message. */
    public $personId; 

    /** @var string The message body. */
    public $body; 

    /** @var string|null The current delivery status of this message. */
    public $status;
}
?>