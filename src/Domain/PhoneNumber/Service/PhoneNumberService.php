<?php 
namespace VirtualPhone\Domain\PhoneNumber\Service;

use VirtualPhone\Domain\PhoneNumber\Repository\PhoneNumberRepository;
use VirtualPhone\Exception\ValidationException;
use Monolog\Logger;

final class PhoneNumberService {

    /** @var PhoneNumberRepository */
    private $repository;

    private $logger;

    public function __construct(PhoneNumberRepository $repository, Logger $logger) {
        $this->repository = $repository;
        $this->logger = $logger;
    }

    public function createPhoneNumber(array $data): int {
        // @TODO validate 

        $phoneId = $this->repository->insertPhoneNumber($data);

        $this->logger->info('Created new phone number record ' . $data['phone_number'] . ' ID: ' . $phoneId);

        return $phoneId;
    }
}