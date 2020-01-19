<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Domain\Repository;

class PhoneRepository extends AbstractRepository
{
    /**
     * @param null $brandLike
     * @param null $modelLike
     * @param null $osLike
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
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

        $nb = $this
            ->createQueryBuilder('u')
            ->select('count(u.id)');

        if ($brandLike) {
            $qb
                ->where('p.brand LIKE ?1')
                ->setParameter(1, '%'.$brandLike.'%')
            ;
            $nb
                ->where('p.brand LIKE ?1')
                ->setParameter(1, '%'.$brandLike.'%')
            ;
        }
        if ($modelLike) {
            $qb
                ->where('p.model LIKE ?2')
                ->setParameter(2, '%'.$modelLike.'%')
            ;
            $nb
                ->where('p.model LIKE ?2')
                ->setParameter(2, '%'.$modelLike.'%')
            ;
        }
        if ($osLike) {
            $qb
                ->where('p.os LIKE ?3')
                ->setParameter(3, '%'.$osLike.'%')
            ;
            $nb
                ->where('p.os LIKE ?3')
                ->setParameter(3, '%'.$osLike.'%')
            ;
        }

        return $this->paginate($qb, $nb, $limit, $offset);
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
