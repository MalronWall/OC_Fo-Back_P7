<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Domain\DTO\Users;

use AppBundle\Domain\Entity\Client;
use Symfony\Component\Validator\Constraints as Assert;

class CreateUserDTO
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $name;
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $firstname;
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $address;
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $cp;
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $city;
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $phoneNumber;
    /**
     * @var Client
     */
    public $client;

    /**
     * CreateUserDTO constructor.
     * @param string|null $name
     * @param string|null $firstname
     * @param string|null $address
     * @param string|null $cp
     * @param string|null $city
     * @param string|null $phoneNumber
     */
    public function __construct(
        ?string $name = null,
        ?string $firstname = null,
        ?string $address = null,
        ?string $cp = null,
        ?string $city = null,
        ?string $phoneNumber = null
    ) {
        $this->name = $name;
        $this->firstname = $firstname;
        $this->address = $address;
        $this->cp = $cp;
        $this->city = $city;
        $this->phoneNumber = $phoneNumber;
    }


}
