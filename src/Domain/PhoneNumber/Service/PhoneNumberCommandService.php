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

    public function createPhoneNumber(array $data): int {
        // @TODO validate 

        $phoneId = $this->repository->insertPhoneNumber($data);

        $this->logger->info('Created new phone number record ' . $data['phone_number'] . ' ID: ' . $phoneId);

        return $phoneId;
    }
}