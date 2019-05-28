<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Helper\Users;

use AppBundle\Domain\DTO\Users\ListUserDTO;
use AppBundle\Domain\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Pagerfanta;
use function PHPSTORM_META\type;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserHelper
{
    /** @var EntityManagerInterface */
    private $entityManager;
    /** @var ValidatorInterface */
    private $validator;

    /**
     * UserHelper constructor.
     * @param EntityManagerInterface $entityManager
     * @param ValidatorInterface $validator
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ) {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    /**
     * @param $name
     * @param $firstname
     * @param $order
     * @param $limit
     * @param $offset
     * @return mixed
     * @throws \Exception
     */
    public function listUser($name, $firstname, $order, $limit, $offset)
    {
        $errors = $this->validator->validate(
            new ListUserDTO($name, $firstname, $order, $limit, $offset)
        );
        if (count($errors)) {
            $i = 0;
            foreach ($errors as $error) {
                $msg["errors"][$i]["on_property"] = $error->getPropertyPath();
                $msg["errors"][$i]["error_message"] = $error->getMessage();
                $msg["errors"][$i]["value_given"] = $error->getInvalidValue();
                $i++;
                    /*
                $msg = $msg . "Property '".$errors->get($key)->getPropertyPath()."': ".
                $errors->get($key)->getMessage()." => ".$errors->get($key)->getInvalidValue()." given.";*/
            }
            $jsonMsg = json_encode($msg, JSON_PRETTY_PRINT);
            throw new \Exception($jsonMsg);
        }
        return $this->entityManager->getRepository(User::class)
            ->listWithPagination($name, $firstname, $order, $limit, $offset);
    }

    /**
     * @param User $user
     */
    public function createUser(User $user)
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    /**
     * @param User $user
     */
    public function deleteUser(User $user)
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }
}
