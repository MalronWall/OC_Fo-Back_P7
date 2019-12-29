<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Domain\Repository;

class PhoneRepository extends AbstractRepository
{
    public function listWithPagination(
        $brandLike = null,
        $modelLike = null,
        $osLike = null,
        $order = 'asc',
        $limit = 10,
        $offset = 0
    ) {
        $qb = $this
            ->createQueryBuilder('p')
            ->orderBy('p.brand', $order)
            ->addOrderBy('p.model', $order)
        ;

        if ($brandLike) {
            $qb
                ->where('p.brand LIKE ?1')
                ->setParameter(1, '%'.$brandLike.'%')
            ;
        }
        if ($modelLike) {
            $qb
                ->where('p.model LIKE ?2')
                ->setParameter(2, '%'.$modelLike.'%')
            ;
        }
        if ($osLike) {
            $qb
                ->where('p.os LIKE ?3')
                ->setParameter(3, '%'.$osLike.'%')
            ;
        }

        return $this->paginate($qb, $limit, $offset);
    }

    /**
     * @return mixed
     */
    public function list()
    {
        return $this->createQueryBuilder('p')

            ->orderBy('p.brand', 'ASC')
            ->orderBy('p.model', 'ASC')

            ->getQuery()
            ->getResult();
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findPhoneById($id)
    {
        return $this->createQueryBuilder('p')
            ->where('p.id = :id')

            ->setParameter('id', $id)

            ->getQuery()
            ->getOneOrNullResult();
    }
}
