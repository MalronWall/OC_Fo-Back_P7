<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Domain\Repository;

class UserRepository extends AbstractRepository
{
    public function listWithPagination(
        $nameLike = null,
        $firstnameLike = null,
        $order = 'asc',
        $limit = 10,
        $offset = 0
    ) {
        $qb = $this
            ->createQueryBuilder('u')
            ->orderBy('u.name', $order)
            ->addOrderBy('u.firstname', $order)
        ;

        if ($nameLike) {
            $qb
                ->where('u.name LIKE ?1')
                ->setParameter(1, '%'.$nameLike.'%')
            ;
        }
        if ($firstnameLike) {
            $qb
                ->where('u.firstname LIKE ?2')
                ->setParameter(2, '%'.$firstnameLike.'%')
            ;
        }

        return $this->paginate($qb, $limit, $offset);
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
        return $this->createQueryBuilder('u')
            ->where('u.id = :id')

            ->setParameter('id', $id)

            ->getQuery()
            ->getOneOrNullResult();
    }
}
