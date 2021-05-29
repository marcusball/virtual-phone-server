<?php

namespace App\Action;

use App\Domain\Test\Service\TestReader;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action
 */
final class TestReadAction {
    
    /**
     * @var TestReader
     */
    private $testReader;

    /**
     * The constructor.
     *
     * @param TestReader $testReader The test reader
     */
    public function __construct(TestReader $testReader) {
        $this->testReader = $testReader;
    }

    /**
     * Invoke.
     *
     * @param ServerRequestInterface $request The request
     * @param ResponseInterface $response The response
     * @param array<mixed> $args The route arguments
     *
     * @return ResponseInterface The response
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args = []
    ): ResponseInterface {
        // Collect input from the HTTP request
        $testId = $args['foo'];

        // Invoke the Domain with inputs and retain the result
        $testData = $this->testReader->getTestDetails($testId);

        // Build the HTTP response
        $response->getBody()->write((string)json_encode(['test' => $testData]));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}