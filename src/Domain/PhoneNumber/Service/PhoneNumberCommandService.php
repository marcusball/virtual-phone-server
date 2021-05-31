<?php 
namespace VirtualPhone\Domain\PhoneNumber\Service;

use VirtualPhone\Domain\PhoneNumber\Repository\PhoneNumberCommandRepository;
use VirtualPhone\Exception\ValidationException;
use Monolog\Logger;

final class PhoneNumberCommandService {

    /** @var PhoneNumberRepository */
    private $repository;

    private $logger;

    public function __construct(PhoneNumberCommandRepository $repository, Logger $logger) {
        $this->repository = $repository;
        $this->logger = $logger;
    }

    public function createPhoneNumber(string $phoneNumber): int {
        // @TODO validate 

        $phoneId = $this->repository->insertPhoneNumber($phoneNumber);

        $this->logger->info('Created new phone number record ' . $phoneNumber . ' ID: ' . $phoneId);

        return $phoneId;
    }
}