<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Domain\Entity;

use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\UuidInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Domain\Repository\ClientRepository")
 * @ORM\Table(name="client")
 */
class Client
{
    /**
     * @var UuidInterface
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
     * @ORM\Column(type="string", unique=true)
     */
    private $username;
    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $password;
    /**
     * @var Collection|Phone[]
     *
     * @ORM\ManyToMany(targetEntity="Phone")
     * @ORM\JoinTable(
     *     name="clients_phones",
     *     joinColumns={@ORM\JoinColumn(name="client_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="phone_id", referencedColumnName="id")}
     * )
     */
    private $phones;
    /**
     * @var Collection|User[]
     *
     * @ORM\ManyToMany(targetEntity="User", inversedBy="clients")
     * @ORM\JoinTable(
     *     name="clients_users",
     *     joinColumns={@ORM\JoinColumn(name="client_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
     * )
     */
    private $users;

    /**
     * Client constructor.
     * @param string $username
     * @param string $password
     */
    public function __construct(
        string $username,
        string $password
    ) {
        $this->username = $username;
        $this->password = $password;
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
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return Phone[]|Collection
     */
    public function getPhones()
    {
        return $this->phones;
    }

    /**
     * @return User[]|Collection
     */
    public function getUsers()
    {
        return $this->users;
    }
}
