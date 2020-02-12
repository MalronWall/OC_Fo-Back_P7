<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Domain\DTO\Security;

use Symfony\Component\Validator\Constraints as Assert;

class ApiLoginDTO
{
    /**
     * @var null|string
     * @Assert\NotBlank()
     */
    public $username;
    /**
     * @var null|string
     * @Assert\NotBlank()
     */
    public $password;

    /**
     * ApiLoginDTO constructor.
     * @param null|string $username
     * @param null|string $password
     */
    public function __construct(
        ?string $username = null,
        ?string $password = null
    ) {
        $this->username = $username;
        $this->password = $password;
    }
}
