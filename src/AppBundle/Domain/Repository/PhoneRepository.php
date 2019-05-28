<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Domain\Repository;

use Doctrine\ORM\EntityRepository;

class PhoneRepository extends EntityRepository
{
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
}
