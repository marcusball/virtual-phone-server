<?php 
namespace VirtualPhone\Action;

use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twilio\Security\RequestValidator;
use VirtualPhone\API\APIResponse;
use VirtualPhone\API\UrlBuilder;
use VirtualPhone\Domain\Contact\Contact;
use VirtualPhone\Domain\Contact\Repository\ContactQueryRepository;
use VirtualPhone\Domain\Contact\Service\ContactCommandService;
use VirtualPhone\Domain\Contact\Service\ContactQueryService;
use VirtualPhone\Domain\Message\Service\MessageCommandService;
use VirtualPhone\Domain\Person\Data\PersonData;
use VirtualPhone\Domain\Person\Service\PersonQueryService;
use VirtualPhone\Domain\PhoneNumber\PhoneNumber;
use VirtualPhone\Domain\PhoneNumber\Service\PhoneNumberCommandService;
use VirtualPhone\Domain\PhoneNumber\Service\PhoneNumberQueryService;

class MessageReceiveAction {

    /**
     * Contains Twilio configuration settings
     *
     * @var array {
     *   @var string $sid
     *   @var string $token
     * }
     */
    private $settings;

    /** @var MessageCommandService */
    private $messageService;

    /** @var ContactQueryService */
    private $contactQueryService;

    /** @var ContactCommandService */
    private $contactCommandService;

    /** @var PhoneNumberQueryService */
    private $phoneQueryService;

    /** @var PhoneNumberCommandService */
    private $phoneCommandService; 

    /** @var PersonQueryService */
    private $personQueryService;

    /** @var Logger */
    private $logger;

    private $b;

    public function __construct(
        ContainerInterface $container,
        MessageCommandService $messageService, 
        ContactQueryService $contactQueryService,
        ContactCommandService $contactCommandService,
        PhoneNumberQueryService $phoneQueryService,
        PhoneNumberCommandService $phoneCommandService,
        PersonQueryService $personQueryService,
        Logger $logger,
        UrlBuilder $b
    ) {
        $this->settings              = $container->get('settings')['twilio'];
        $this->messageService        = $messageService;
        $this->contactQueryService   = $contactQueryService;
        $this->contactCommandService = $contactCommandService;
        $this->phoneQueryService     = $phoneQueryService;
        $this->phoneCommandService   = $phoneCommandService;
        $this->personQueryService    = $personQueryService;
        $this->logger                = $logger; 
        $this->b = $b;
    }

    /**
     * Invoke.
     *
     * @param ServerRequestInterface $request The request
     * @param ResponseInterface $response The response
     *
     * @return ResponseInterface The response
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        if (IS_PRODUCTION) {
            $signature = $request->getHeader('X-TWILIO-SIGNATURE')[0];
            $url       = $request->getUri()->__toString();
            $validator = new RequestValidator($this->settings['token']);

            if (!$validator->validate($signature, $url, $_POST)) {
                $this->logger->error('Twilio signature verification failed!');
                
                return APIResponse::error($response, 'Signature verification failed', 400)->into();
            }
            else {
                $this->logger->debug('Twilio signature successfully verified. 👍');
            }
        }

        $data = (array)$request->getParsedBody();

        $sid    = $data['SmsSid'];
        $body   = $data['Body'];
        $from   = $data['From'];
        $to     = $data['To'];
        $status = $data['SmsStatus'];

        $this->logger->info(sprintf(
            'New message received! SID: %s, FROM: %s, TO: %s, STATUS: %s, BODY: "%s"',
            $sid,
            $from,
            $to,
            $status,
            $body
        ));
        
        // Get the phone number record corresponding to who sent the message. 
        $fromPhone = $this->getOrCreatePhoneNumber($from);

        // Get the phone number to which this message was sent. 
        $toPhone = $this->phoneQueryService->getByNumber($to);

        // The $toPhone should ALWAYS exist. 
        if (!$toPhone) {
            $this->logger->error('Failed to find record for RECEIVING phone number "' . $to . '"!');
            return APIResponse::fail($response, 'Internal Server Error', 500)->into();
        }

        try {
            // Throws if no person was found, which should be impossible. 
            $person = $this->personQueryService->getByPhoneId($toPhone->id);
        }
        catch(\Exception $e) {
            $this->logger->error('Failed to retrieve person corresponding to Phone Number ' . $toPhone->id . '!');
            return APIResponse::fail($response, 'Internal Server Error', 500)->into();
        }

        // Get the contact from whom the message was sent. 
        $contact  = $this->getOrCreateContact($fromPhone, $person);

        $messageId = $this->messageService->receive(
            $sid, $person->id, $contact, $body, $status
        );

        return APIResponse::success($response, ['id' => $messageId], 'message', 201)->into();
    }

    /**
     * Fetch a phone number record by full $phoneNumber, and create one if no such record exists.
     *
     * @param string $phoneNumber
     * @return PhoneNumber
     */
    private function getOrCreatePhoneNumber(string $phoneNumber): PhoneNumber {
        // Query to see if we already have a record for this phone number. 
        $phone = $this->phoneQueryService->getByNumber($phoneNumber);

        // If we do, return the phone number, we're done. 
        if ($phone !== null) {
            return $phone;
        }

        $this->logger->info('Received message from unknown number; creating record for "' . $phoneNumber . '".');

        // Create a record for the phone number. 
        $phoneId = $this->phoneCommandService->createPhoneNumber($phoneNumber);

        $this->logger->info('Phone Number record ' . $phoneId . ' created for "' . $phoneNumber . '".');

        // Query the newly created phone number and return it. 
        return $this->phoneQueryService->getById($phoneId);
    }

    /**
     * Fetch a $person's Contact record given the contact's $phone, and create one if no such record exists.
     *
     * @param PhoneNumber $phone
     * @param PersonData $person
     * @return Contact
     */
    private function getOrCreateContact(PhoneNumber $phone, PersonData $person): Contact {
        $contact = $this->contactQueryService->getByPhoneId($phone->id, $person->id);

        if ($contact !== null) {
            return $contact;
        }

        $this->logger->info('Person ' . $person->id . ' has no contact for Phone Number (' . $phone->id . '); creating one...');

        $contactId = $this->contactCommandService->createContact([
            'person_id'   => $person->id,
            'phone_id'    => $phone->id,
            'name'        => null,
            'description' => null,
        ]);

        $this->logger->info(sprintf('Contact %s created for Person %s', $contactId, $person->id));

        return $this->contactQueryService->getById($contactId, $person->id);
    }
}