<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Domain\DTO\Clients;

use Symfony\Component\Validator\Constraints as Assert;

class CreateClientDTO
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $username;
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 6
     * )
     */
    public $password;

    /**
     * CreateClientDTO constructor.
     * @param null|string $username
     * @param null|string $password
     */
    public function __construct(
        ?string $username,
        ?string $password
    ) {
        $this->username = $username;
        $this->password = $password;
    }


}
