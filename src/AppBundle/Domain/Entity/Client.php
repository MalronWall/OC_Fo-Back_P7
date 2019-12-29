<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Domain\Entity;

use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\UuidInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Domain\Repository\ClientRepository")
 * @ORM\Table(name="client")
 */
class Client implements UserInterface
{
    /**
     * @var UuidInterface
     *
     * @Groups({"client_list"})
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
     * @Groups({"client_list", "client_detail"})
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
     * @var string[]
     *
     * @Groups({"client_detail"})
     *
     * @ORM\Column(type="array")
     */
    private $roles;
    /**
     * @var Collection|Phone[]
     *
     * @Groups({"client_detail"})
     *
     * @ORM\OneToMany(targetEntity="Phone", mappedBy="client")
     */
    private $phones;
    /**
     * @var Collection|User[]
     *
     * @Groups({"client_detail"})
     *
     * @ORM\OneToMany(targetEntity="User", mappedBy="client")
     */
    private $users;

    /**
     * Client constructor.
     * @param string $username
     * @param string $password
     * @param string $role
     */
    public function __construct(
        string $username,
        string $password,
        string $role = "ROLE_CLIENT"
    ) {
        $this->username = $username;
        $this->password = $password;
        $this->roles[] = $role;
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

    /**
     * Returns the roles granted to the user.
     *
     *     public function getRoles()
     *     {
     *         return ['ROLE_USER'];
     *     }
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return array (Role|string)[] The user roles
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        return;
    }
}
