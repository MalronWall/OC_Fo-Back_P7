<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Domain\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

class AbstractRepository extends EntityRepository
{
    /**
     * @param QueryBuilder $qb
     * @param int $limit
     * @param int $offset
     * @return Pagerfanta
     */
    protected function paginate(QueryBuilder $qb, $limit = 10, $offset = 0)
    {
        if ($limit <= 0) {
            throw new \LogicException('$limit must be greater than or equal to 0.');
        }
        if ($offset < 0) {
            throw new \LogicException('$offset must be greater than 0.');
        }

        $pager = new Pagerfanta(new DoctrineORMAdapter($qb));
        $currentPage = $offset + 1;

        $pager->setMaxPerPage((int) $limit);
        $pager->setCurrentPage($currentPage);

        return $pager;
    }
}
