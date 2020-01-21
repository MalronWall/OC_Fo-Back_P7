<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Domain\Repository;

class UserRepository extends AbstractRepository
{
    /**
     * @param null $nameLike
     * @param null $firstnameLike
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @param string $clientId
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function listWithPagination(
        $nameLike = null,
        $firstnameLike = null,
        $order = 'asc',
        $limit = 10,
        $offset = 0,
        $clientId = ""
    ): array {
        $qb = $this
            ->createQueryBuilder('u')
            ->orderBy('u.name', $order)
            ->addOrderBy('u.firstname', $order)
            ->where('u.client = :client')
            ->setParameter("client", $clientId);
        $nb = $this
            ->createQueryBuilder('u')
            ->select('count(u.id)')
            ->where('u.client = :client')
            ->setParameter("client", $clientId);

        if ($nameLike) {
            $qb
                ->where('u.name LIKE ?1')
                ->setParameter(1, '%' . $nameLike . '%');
            $nb
                ->where('u.name LIKE ?1')
                ->setParameter(1, '%' . $nameLike . '%');
        }
        if ($firstnameLike) {
            $qb
                ->where('u.firstname LIKE ?2')
                ->setParameter(2, '%' . $firstnameLike . '%');
            $nb
                ->where('u.firstname LIKE ?2')
                ->setParameter(2, '%' . $firstnameLike . '%');
        }

        return $this->paginate($qb, $nb, $limit, $offset);
    }

    /**
     * @return mixed
     */
    public function list()
    {
        return $this->createQueryBuilder('u')
                    ->orderBy('u.name', 'ASC')
                    ->addOrderBy('u.firstname', 'ASC')
                    ->getQuery()
                    ->getResult();
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findUserById($id)
    {
        $qb = $this->createQueryBuilder('u')
                   ->where('u.id = :id')
                   ->setParameter('id', $id);

        return $this->getResultAsArray($qb);
    }
}
