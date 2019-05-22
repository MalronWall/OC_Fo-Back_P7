<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Domain\Entity;

use Ramsey\Uuid\UuidInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="user")
 */
class User
{
    /**
     * @var UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;
    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $firstname;
    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $name;
    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $address;
    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $cp;
    /**
     * @var string
     *
     * @ORM\Column(type="string", name="phoneNumber")
     */
    private $phoneNumber;

    /**
     * User constructor.
     * @param string $firstname
     * @param string $name
     * @param string $address
     * @param string $cp
     * @param string $phoneNumber
     */
    public function __construct(
        string $firstname,
        string $name,
        string $address,
        string $cp,
        string $phoneNumber
    ) {
        $this->firstname = $firstname;
        $this->name = $name;
        $this->address = $address;
        $this->cp = $cp;
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFirstname(): string
    {
        return $this->firstname;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @return string
     */
    public function getCp(): string
    {
        return $this->cp;
    }

    /**
     * @return string
     */
    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }
}
