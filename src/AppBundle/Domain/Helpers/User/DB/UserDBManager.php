<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Domain\Helpers\User\DB;

use AppBundle\Domain\DTO\Users\CreateUserDTO;
use AppBundle\Domain\DTO\Users\ListUserDTO;
use AppBundle\Domain\Entity\User;
use AppBundle\Domain\Helpers\Common\ExceptionManager;
use Doctrine\ORM\EntityManagerInterface;

class UserDBManager
{
    /** @var \AppBundle\Domain\Repository\UserRepository|\Doctrine\Common\Persistence\ObjectRepository */
    private $userRepo;
    /** @var ExceptionManager */
    private $exceptionManager;

    /**
     * ClientDBManager constructor.
     * @param EntityManagerInterface $entityManager
     * @param ExceptionManager $exceptionManager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ExceptionManager $exceptionManager
    ) {
        $this->userRepo = $entityManager->getRepository(User::class);
        $this->exceptionManager = $exceptionManager;
    }

    /**
     * @param ListUserDTO $dto
     * @param string $clientId
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function listUser(ListUserDTO $dto, string $clientId): array
    {
        return $this->userRepo->listWithPagination(
            $dto->name,
            $dto->firstname,
            $dto->order,
            $dto->limit,
            $dto->offset,
            $clientId
        );
    }

    /**
     * @param $id
     * @param $clientId
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function existUser($id, ?string $clientId = null)
    {
        $result = $this->userRepo->findUserById($id);
        if (is_null($result)) {
            $this->exceptionManager->pageNotFoundExceptionToJson($id);
        }
        if ($result["datas"][0]->getClient()->getId()->toString() != $clientId) {
            $this->exceptionManager->unauthorizedAccessExceptionToJson($id);
        }
        return $result;
    }

    /**
     * @param CreateUserDTO $dto
     * @return mixed
     */
    public function createUser(CreateUserDTO $dto)
    {
        $user = new User(
            $dto->firstname,
            $dto->name,
            $dto->address,
            $dto->cp,
            $dto->city,
            $dto->phoneNumber,
            $dto->client["datas"][0]
        );
        return $this->userRepo->create($user);
    }

    /**
     * @param $user
     */
    public function delete($user)
    {
        return $this->userRepo->delete($user);
    }
}
