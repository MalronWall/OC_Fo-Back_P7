<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Domain\Helpers\User\DB;

use AppBundle\Domain\DTO\Users\CreateUserDTO;
use AppBundle\Domain\DTO\Users\ListUserDTO;
use AppBundle\Domain\Entity\User;
use AppBundle\Domain\Helpers\Common\EasyEntityManager;
use AppBundle\Domain\Helpers\Common\ExceptionManager;
use Doctrine\ORM\EntityManagerInterface;

class UserDBManager extends EasyEntityManager
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
        parent::__construct($entityManager);
        $this->userRepo = $this->entityManager->getRepository(User::class);
        $this->exceptionManager = $exceptionManager;
    }

    /**
     * @param ListUserDTO $dto
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function listUser(ListUserDTO $dto)
    {
        return $this->userRepo->listWithPagination($dto->name, $dto->firstname, $dto->order, $dto->limit, $dto->offset);
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function existUser($id)
    {
        $result = $this->userRepo->findUserById($id);
        if (is_null($result)) {
            $this->exceptionManager->pageNotFoundExceptionToJson($id);
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
            $dto->client
        );
        return $this->create($user);
    }
}
