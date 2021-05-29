<?php

namespace App\Domain\Test\Service;

use App\Domain\Test\Data\TestReaderData;
use App\Domain\Test\Repository\TestReaderRepository;
use App\Exception\ValidationException;

/**
 * Service.
 */
final class TestReader {
    
    /**
     * @var TestReaderRepository
     */
    private $repository;

    /**
     * The constructor.
     *
     * @param TestReaderRepository $repository The repository
     */
    public function __construct(TestReaderRepository $repository) {
        $this->repository = $repository;
    }

    /**
     * Read a test by the given test id.
     *
     * @param string $testFoo The value of the test's $foo.
     * @throws ValidationException
     * @return TestReaderData The test data
     */
    public function getTestDetails(string $testFoo): TestReaderData {
        // Validation
        if (empty($testFoo)) {
            throw new ValidationException('Test Foo required');
        }

        $test = $this->repository->getTestByFoo($testFoo);

        return $test;
    }
}