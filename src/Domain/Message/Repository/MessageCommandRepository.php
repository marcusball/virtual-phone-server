<?php 
namespace VirtualPhone\Domain\Message\Repository;

use Monolog\Logger;
use VirtualPhone\Domain\Message\Data\CreateOutboundMessageCommandData;
use Twilio\Rest\Client;
use PDO;
use VirtualPhone\API\UrlBuilder;

final class MessageCommandRepository {

    private $db;

    private $twilio;

    private $logger; 

    private $urlBuilder; 

    public function __construct(PDO $db, Client $twilio, UrlBuilder $builder, Logger $logger) {
        $this->db = $db;
        $this->twilio = $twilio;
        $this->logger = $logger;
        $this->urlBuilder = $builder;
    }

    public function create(CreateOutboundMessageCommandData $data): int {
        // Create a record of this message in the database. 
        $messageId = $this->createMessageRecord($data);

        $message = $this->twilio
            ->messages
            ->create(
                $data->to, [
                    'from' => $data->from,
                    'body' => $data->body,
                    'statusCallback' => $this->urlBuilder->fullUrlFor('message-status-webhook', ['messageId' => $messageId])
                ]
            );

        // Add Twilio's unique SID that identifies this message. 
        $this->setMessageSid($messageId, $message->sid);

        $this->logger->info(
            sprintf(
                'SMS Message sent; TO: %s, FROM: %s, SID: %s, STATUS: %s',
                $message->to,
                $message->from,
                $message->sid,
                $message->status
            )
        );

        return $messageId;
    }

    /**
     * Create a database record for this message. 
     * Should be used before a message is sent.
     *
     * @param CreateOutboundMessageCommandData $data
     * @return integer
     */
    private function createMessageRecord(CreateOutboundMessageCommandData $data): int {
        $sql = 
            'INSERT INTO message (person_id, contact_id, body, status)
            VALUES (:pid, :cid, :body, :st)
            RETURNING id
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':pid'  => $data->personId,
            ':cid'  => $data->contactId,
            ':body' => $data->body,
            ':st'   => 'unknown',
        ]);
        $stmt->setFetchMode(PDO::FETCH_COLUMN, 0);
        $messageId = $stmt->fetch();

        return $messageId;
    }

    /**
     * Set Twilio's SID on a given message.
     *
     * @param integer $messageId
     * @param string $sid
     * @return void
     */
    private function setMessageSid(int $messageId, string $sid) {
        $sql = 
            'UPDATE message
            SET sid = :sid
            WHERE id = :mid
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':mid' => $messageId,
            ':sid' => $sid,
        ]);
    }

    public function updateStatus(int $messageId, string $status) {
        $sql =
            'UPDATE message
            SET status = :status
            WHERE id = :mid
            -- Only update if the existing status is not a "final state". 
            -- If it is already in one of these states, then we are just receiving
            -- an old status; it is not possible to move out of thone of these states. 
            AND status NOT IN (\'delivered\', \'undelivered\', \'failed\')
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':mid' => $messageId,
            ':status' => $status,
        ]);
    }
}