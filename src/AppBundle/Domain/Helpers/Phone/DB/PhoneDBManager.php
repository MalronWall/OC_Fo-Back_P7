<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Domain\Helpers\Phone\DB;

use AppBundle\Domain\DTO\Phones\CreatePhoneDTO;
use AppBundle\Domain\DTO\Phones\ListPhoneDTO;
use AppBundle\Domain\DTO\Users\CreateUserDTO;
use AppBundle\Domain\DTO\Users\ListUserDTO;
use AppBundle\Domain\Entity\Phone;
use AppBundle\Domain\Entity\User;
use AppBundle\Domain\Helpers\Common\EasyEntityManager;
use AppBundle\Domain\Helpers\Common\ExceptionManager;
use Doctrine\ORM\EntityManagerInterface;

class PhoneDBManager extends EasyEntityManager
{
    /** @var \AppBundle\Domain\Repository\PhoneRepository|\Doctrine\Common\Persistence\ObjectRepository */
    private $phoneRepo;
    /** @var ExceptionManager */
    private $exceptionManager;

    /**
     * PhoneDBManager constructor.
     * @param EntityManagerInterface $entityManager
     * @param ExceptionManager $exceptionManager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ExceptionManager $exceptionManager
    ) {
        parent::__construct($entityManager);
        $this->phoneRepo = $this->entityManager->getRepository(Phone::class);
        $this->exceptionManager = $exceptionManager;
    }

    /**
     * @param ListPhoneDTO $dto
     * @return mixed
     */
    public function listPhone(ListPhoneDTO $dto)
    {
        return $this->phoneRepo->listWithPagination(
            $dto->brand,
            $dto->model,
            $dto->os,
            $dto->order,
            $dto->limit,
            $dto->offset
        );
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function existPhone($id)
    {
        $result = $this->phoneRepo->findPhoneById($id);
        if (is_null($result)) {
            $this->exceptionManager->pageNotFoundExceptionToJson($id);
        }
        return $result;
    }

    /**
     * @param CreatePhoneDTO $dto
     * @return mixed
     */
    public function createPhone(CreatePhoneDTO $dto)
    {
        $user = new Phone(
            $dto->brand,
            $dto->model,
            $dto->os,
            $dto->price,
            $dto->client,
            $dto->cpu,
            $dto->gpu,
            $dto->ram,
            $dto->memory,
            $dto->dimensions,
            $dto->weight,
            $dto->resolution,
            $dto->mainCamera,
            $dto->selfieCamera,
            $dto->sound,
            $dto->battery,
            $dto->colors
        );
        return $this->create($user);
    }
}
