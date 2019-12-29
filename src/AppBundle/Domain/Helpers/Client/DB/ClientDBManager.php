<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Domain\Helpers\Client\DB;

use AppBundle\Domain\DTO\Clients\CreateClientDTO;
use AppBundle\Domain\Entity\Client;
use AppBundle\Domain\Helpers\Common\EasyEntityManager;
use AppBundle\Domain\Helpers\Common\ExceptionManager;
use AppBundle\Domain\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;

class ClientDBManager extends EasyEntityManager
{
    /** @var \AppBundle\Domain\Repository\ClientRepository|\Doctrine\Common\Persistence\ObjectRepository */
    private $clientRepo;
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
        $this->clientRepo = $this->entityManager->getRepository(Client::class);
        $this->exceptionManager = $exceptionManager;
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function existClient($id)
    {
        $result = $this->clientRepo->findClientById($id);
        if (is_null($result)) {
            $this->exceptionManager->pageNotFoundExceptionToJson($id);
        }
        return $result;
    }

    /**
     * @param CreateClientDTO $dto
     * @return mixed
     */
    public function createClient(CreateClientDTO $dto)
    {
        $client = new Client(
            $dto->username,
            $dto->password
        );
        return $this->create($client);
    }
}
