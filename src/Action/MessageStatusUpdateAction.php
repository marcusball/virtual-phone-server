<?php
namespace VirtualPhone\Action;

use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twilio\Security\RequestValidator;
use VirtualPhone\API\APIResponse;
use VirtualPhone\Domain\Message\Service\MessageCommandService;
use VirtualPhone\Exception\ValidationException;

class MessageStatusUpdateAction {

    /**
     * Contains Twilio configuration settings
     *
     * @var array {
     *   @var string $sid
     *   @var string $token
     * }
     */
    private $settings;

    /** @var Logger */
    private $logger;

    /** @var MessageCommandService */
    private $service;

    public function __construct(MessageCommandService $service, ContainerInterface $container, Logger $logger) {
        $this->settings = $container->get('settings')['twilio'];
        $this->logger = $logger;
        $this->service = $service;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        if (IS_PRODUCTION) {
            $signature = $request->getHeader('HTTP-X-TWILIO-SIGNATURE')[0];
            $url       = $request->getUri()->__toString();
            $validator = new RequestValidator($this->settings['token']);

            if (!$validator->validate($signature, $url, $_POST)) {
                $this->logger->error('Twilio signature verification failed!');

                $response->getBody()->write(json_encode(['error' => 'Signature verification failed']));
                return $response->withStatus(400);
            }
        }

        $params = (array)$request->getParsedBody();
        $messageId = $args['messageId'];
        $messageSid = $params['SmsSid'];
        $messageStatus = $params['SmsStatus'];

        try {
            $this->service->updateStatus($messageId, $messageStatus);
        }
        catch (ValidationException $e) {
            return APIResponse::error($response, $e->getMessage(), 400)->into();
        }

        return APIResponse::from($response)
            ->withMessage('Status updated')
            ->into();
    }
}