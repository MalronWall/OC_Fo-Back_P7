<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Domain\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Domain\Repository\UserRepository")
 * @ORM\Table(name="user")
 */
class User
{
    /**
     * @var UuidInterface
     *
     * @Groups({"user_list"})
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Doctrine\ORM\Id\UuidGenerator")
     */
    private $id;
    /**
     * @var string
     *
     * @Groups({"user_list", "user_detail"})
     *
     * @ORM\Column(type="string")
     */
    private $firstname;
    /**
     * @var string
     *
     * @Groups({"user_list", "user_detail"})
     *
     * @ORM\Column(type="string")
     */
    private $name;
    /**
     * @var string
     *
     * @Groups({"user_detail"})
     *
     * @ORM\Column(type="string")
     */
    private $address;
    /**
     * @var string
     *
     * @Groups({"user_detail"})
     *
     * @ORM\Column(type="string")
     */
    private $cp;
    /**
     * @var string
     *
     * @Groups({"user_detail"})
     *
     * @ORM\Column(type="string")
     */
    private $city;
    /**
     * @var string
     *
     * @Groups({"user_detail"})
     *
     * @ORM\Column(type="string", name="phoneNumber")
     */
    private $phoneNumber;
    /**
     * @var Collection|Client[]
     *
     * @Groups({"user_list", "user_detail"})
     *
     * @ORM\ManyToMany(targetEntity="Client", mappedBy="users")
     */
    private $clients;

    /**
     * User constructor.
     * @param string $firstname
     * @param string $name
     * @param string $address
     * @param string $cp
     * @param string $city
     * @param string $phoneNumber
     */
    public function __construct(
        string $firstname,
        string $name,
        string $address,
        string $cp,
        string $city,
        string $phoneNumber
    ) {
        $this->firstname = $firstname;
        $this->name = $name;
        $this->address = $address;
        $this->cp = $cp;
        $this->city = $city;
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
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    /**
     * @return Client[]|Collection
     */
    public function getClients()
    {
        return $this->clients;
    }
}
