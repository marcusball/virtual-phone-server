<?php 
namespace VirtualPhone\API;

use Psr\Http\Message\ResponseInterface;

class APIResponse implements \JsonSerializable {

    const STATUS_SUCCESS = 'success';
    const STATUS_ERROR   = 'error';
    const STATUS_FAIL    = 'fail';

    private $response;

    private $status = self::STATUS_SUCCESS;

    private $data = null;

    private $messages = [];

    private function __construct(ResponseInterface $response) {
        $this->response = $response;
    }

    /**
     * Start building a new APIResponse
     *
     * @param ResponseInterface $response
     * @return APIResponse
     */
    public static function from(ResponseInterface $response): APIResponse {
        return new self($response);
    }

    /**
     * Construct a new "success" API response with the given $data and status $code.
     * 
     * @param ResponseInterface $response
     * @param mixed             $data The response data.
     * @param string|null       $dataKey If present, the property name in which $data will be placed within the response's $data object. 
     * @param int|null          $code The HTTP status code to return.
     * @return APIResponse
     */
    public static function success(ResponseInterface $response, $data, ?string $dataKey = null, ?int $code = null): APIResponse {
        return self::from($response)
            ->asSuccess()
            ->withData($data, $dataKey)
            ->withStatus($code);
    }

    /**
     * Construct a new "error" API response with the given $messages and status $code.
     * 
     * @param ResponseInterface    $response
     * @param string|string[]|null $messages The message, or messages, to return with the response. 
     * @param int|null             $code The HTTP status code to return.
     * @return APIResponse
     */
    public static function error(ResponseInterface $response, string|array|null $messages = null, ?int $code = null): APIResponse {
        return self::from($response)
            ->asError()
            ->addMessageAny($messages)
            ->withStatus($code);
    }

    /**
     * Construct a new "fail" API response with the given $messages and status $code.
     * 
     * @param ResponseInterface    $response
     * @param string|string[]|null $messages The message, or messages, to return with the response. 
     * @param int|null             $code The HTTP status code to return.
     * @return APIResponse
     */
    public static function fail(ResponseInterface $response, string|array|null $messages = null, ?int $code = null): APIResponse {
        return self::from($response)
            ->asFail()
            ->addMessageAny($messages)
            ->withStatus($code);
    }

    /**
     * Set the response status as "success".
     *
     * @return APIResponse
     */
    public function asSuccess(): APIResponse {
        $this->status = self::STATUS_SUCCESS;
        return $this;
    }

    /**
     * Set the response status as "error".
     *
     * @return APIResponse
     */
    public function asError(): APIResponse {
        $this->status = self::STATUS_ERROR;
        return $this;
    }

    /**
     * Set the response status as "fail".
     *
     * @return APIResponse
     */
    public function asFail(): APIResponse {
        $this->status = self::STATUS_FAIL;
        return $this;
    }

    /**
     * Set the HTTP status code to return with the response. 
     *
     * @param integer|null $code
     * @param string $reasonPhrase
     * @return APIResponse
     */
    public function withStatus(?int $code, string $reasonPhrase = ''): APIResponse {
        if (is_null($code)) {
            return $this;
        }

        $this->response->withStatus($code, $reasonPhrase);
        return $this; 
    }

    /**
     * Set the data to return with the response.
     *
     * @param [type] $data
     * @param string|null $dataKey 
     *      If omitted, the response will be returned like ['data' => $data].
     *      If present, the response will be formatted as ['data' => [$dataKey => $data]].
     * @return APIResponse
     */
    public function withData($data, ?string $dataKey = null): APIResponse {
        if (is_null($dataKey)) {
            $this->data = $data;
        }
        else {
            $this->data[$dataKey] = $data;
        }

        return $this;
    }

    /**
     * Add a message to the array of response messages.
     *
     * @param string $message
     * @return APIResponse
     */
    public function withMessage(string $message): APIResponse {
        return $this->addMessage($message);
    }

    /**
     * Set (and override, if any are already present) the array of response messages.
     *
     * @param array $messages
     * @return APIResponse
     */
    public function withMessages(array $messages): APIResponse {
        $this->messages = $messages;
        return $this;
    }

    /**
     * Add a message to the array of response messages.
     *
     * @param string $message
     * @return APIResponse
     */
    public function addMessage(string $message): APIResponse {
        $this->messages[] = $message;
        return $this;
    }

    /**
     * Merge an array of $messages into the array of response messages.
     *
     * @param array $messages
     * @return APIResponse
     */
    public function addMessages(array $messages): APIResponse {
        $this->messages = array_merge($this->messages, $messages);
        return $this;
    }

    private function addMessageAny(string|array|null $messages): APIResponse {
        if (is_string($messages)) {
            return $this->addMessage($messages);
        }
        else if (is_array($messages)) {
            return $this->addMessages($messages);
        }
        else {
            return $this;
        }
    }

    public function jsonSerialize() {
        return [
            'status'   => $this->status,
            'data'     => $this->data,
            'messages' => $this->messages,
        ];
    }

    /**
     * Return a constructed ResponseInterface object.
     *
     * @return ResponseInterface
     */
    public function into(): ResponseInterface {
        $this->response->getBody()->write(json_encode($this));
        return $this->response;
    }
}