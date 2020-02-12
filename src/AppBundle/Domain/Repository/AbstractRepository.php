<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Domain\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AbstractRepository extends EntityRepository
{
    /**
     * @param QueryBuilder $qb
     * @param QueryBuilder $nb
     * @param int $limit
     * @param int $offset
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    protected function paginate(QueryBuilder $qb, QueryBuilder $nb, $limit = 10, $offset = 0) : array
    {
        $limit = (int)$limit;
        $offset = (int)$offset;

        if ($limit <= 0) {
            throw new \LogicException('$limit must be greater than or equal to 0.');
        }
        if ($offset < 0) {
            throw new \LogicException('$offset must be greater than 0.');
        }

        if ($limit) {
            $qb->setMaxResults($limit);
        }
        if ($offset) {
            $qb->setFirstResult($offset);
        }

        $nbItems = (int)$nb->getQuery()->getSingleScalarResult();
        $results = $qb->getQuery()->getResult();

        return [
            "datas" => $results,
            "metas" => [
                "nbTotalOfItems" => $nbItems,
                "currentPage" => ceil(($offset+1)/$limit),
                "totalPages" => ceil($nbItems/$limit),
            ]
        ];
    }

    /**
     * @param QueryBuilder $qb
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getResultAsArray(QueryBuilder $qb) : array
    {
        return $this->format($qb->getQuery()->getOneOrNullResult());
    }

    public function create($entity)
    {
        $this->_em->persist($entity);
        $this->_em->flush();
        return $this->format($entity);
    }

    public function update()
    {
        $this->_em->flush();
    }

    public function delete($entity)
    {
        $this->_em->remove($entity);
        $this->_em->flush();
    }

    private function format($entity)
    {
        return [
            "datas" => [$entity]
        ];
    }
}
