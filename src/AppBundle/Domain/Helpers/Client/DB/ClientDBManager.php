<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Domain\Helpers\Client\DB;

use AppBundle\Domain\DTO\Clients\CreateClientDTO;
use AppBundle\Domain\Entity\Client;
use AppBundle\Domain\Helpers\Common\ExceptionManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class ClientDBManager
{
    /** @var \AppBundle\Domain\Repository\ClientRepository|\Doctrine\Common\Persistence\ObjectRepository */
    private $clientRepo;
    /** @var ExceptionManager */
    private $exceptionManager;
    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    /**
     * ClientDBManager constructor.
     * @param EntityManagerInterface $entityManager
     * @param ExceptionManager $exceptionManager
     * @param EncoderFactoryInterface $encoderFactory
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ExceptionManager $exceptionManager,
        EncoderFactoryInterface $encoderFactory
    ) {
        $this->clientRepo = $entityManager->getRepository(Client::class);
        $this->exceptionManager = $exceptionManager;
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function existClientById($id)
    {
        $result = $this->clientRepo->findClientById($id);
        if (is_null($result)) {
            $this->exceptionManager->pageNotFoundExceptionToJson($id);
        }
        return $result;
    }

    /**
     * @param $username
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function existClientByUsername($username)
    {
        $result = $this->clientRepo->findClientByUsername($username);
        if (!is_null($result)) {
            $this->exceptionManager->conflictExceptionToJson($username);
        }
    }

    /**
     * @param CreateClientDTO $dto
     * @return mixed
     */
    public function createClient(CreateClientDTO $dto)
    {
        $client = new Client(
            $dto->username,
            $this->encoderFactory->getEncoder(Client::class)
                                 ->encodePassword($dto->password, null)
        );
        return $this->clientRepo->create($client);
    }
}
