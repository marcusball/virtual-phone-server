<?php

namespace App\Domain\Test\Repository;

use App\Domain\Test\Data\TestReaderData;
use DomainException;
use PDO;

/**
 * Repository.
 */
class TestReaderRepository {

    /**
     * @var PDO The database connection
     */
    private $connection;

    /**
     * Constructor.
     *
     * @param PDO $connection The database connection
     */
    public function __construct(PDO $connection) {
        $this->connection = $connection;
    }

    /**
     * Get test value with the given $foo string.
     *
     * @param string $foo The value of $foo.
     * @throws DomainException
     * @return TestReaderData The test data
     */
    public function getTestByFoo(string $foo): TestReaderData {
        $sql = "SELECT :foo AS foo, 'world!' AS hello";
        $statement = $this->connection->prepare($sql);
        $statement->execute([':foo' => $foo]);

        $statement->setFetchMode(PDO::FETCH_CLASS, TestReaderData::class);
        $test = $statement->fetch();

        if (!$test) {
            throw new DomainException(sprintf('The impossible has happened, impossible to foo with value: %s', $foo));
        }

        return $test;
    }
}