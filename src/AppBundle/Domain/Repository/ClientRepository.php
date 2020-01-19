<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Domain\Repository;

class ClientRepository extends AbstractRepository
{
    /**
     * @return mixed
     */
    public function list()
    {
        return $this->createQueryBuilder('c')

            ->orderBy('c.username', 'ASC')

            ->getQuery()
            ->getResult();
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findClientById($id)
    {
        return $this->createQueryBuilder('c')
            ->where('c.id = :id')

            ->setParameter('id', $id)

            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param $username
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findClientByUsername($username)
    {
        return $this->createQueryBuilder('c')
            ->where('c.username = :username')

            ->setParameter('username', $username)

            ->getQuery()
            ->getOneOrNullResult();
    }
}
