<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Domain\Repository;

use Doctrine\ORM\EntityRepository;

class ClientRepository extends EntityRepository
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
}
