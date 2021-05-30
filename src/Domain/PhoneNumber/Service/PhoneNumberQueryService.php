<?php 
namespace VirtualPhone\Domain\PhoneNumber\Service;

use VirtualPhone\Domain\PhoneNumber\PhoneNumber;
use VirtualPhone\Domain\PhoneNumber\Data\PhoneNumberData;
use VirtualPhone\Domain\PhoneNumber\Repository\PhoneNumberQueryRepository;
use VirtualPhone\Exception\ValidationException;

final class PhoneNumberQueryService {

    /** @var PhoneNumberQueryRepository */
    private $repository;

    public function __construct(PhoneNumberQueryRepository $repository) {
        $this->repository = $repository;
    }

    /**
     * Get a Phone Number given its $id.
     *
     * @param integer $id The Phone Number ID.
     * @return PhoneNumber|bool The Phone Number data, or false if not found.
     * @throws ValidationException
     */
    public function getById(int $id): PhoneNumber|bool {
        if ($id <= 0) {
            throw new ValidationException('Invalid Phone Number ID!');
        }

        $data = $this->repository->getById($id);

        $phoneNumber = new PhoneNumber;
        $phoneNumber->id          = $data->id;
        $phoneNumber->phoneNumber = $data->phoneNumber;
        $phoneNumber->createdAt   = $data->createdAt;
        $phoneNumber->updatedAt   = $data->createdAt;

        return $phoneNumber;
    }
}