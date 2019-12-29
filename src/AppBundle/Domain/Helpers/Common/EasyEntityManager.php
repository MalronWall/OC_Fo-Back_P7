<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Domain\Helpers\Common;

use Doctrine\ORM\EntityManagerInterface;

class EasyEntityManager
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create($entity)
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
        return $entity;
    }

    public function update()
    {
        $this->entityManager->flush();
    }

    public function delete($entity)
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }
}
