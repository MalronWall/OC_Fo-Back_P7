<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Domain\DTO\Users;

use Symfony\Component\Validator\Constraints as Assert;

class ListUserDTO
{
    /**
     * @var null|string
     */
    public $name;
    /**
     * @var null|string
     */
    public $firstname;
    /**
     * @var null|string
     * @Assert\Choice({"asc", "desc"})
     */
    public $order;
    /**
     * @var string|null
     * @Assert\GreaterThan(value="0")
     */
    public $limit;
    /**
     * @var string|null
     * @Assert\GreaterThanOrEqual(value="0")
     */
    public $offset;

    /**
     * listUserDTO constructor.
     * @param $name
     * @param $firstname
     * @param $order
     * @param $limit
     * @param $offset
     */
    public function __construct(
        ?string $name,
        ?string $firstname,
        ?string $order,
        ?string $limit,
        ?string $offset
    ) {
        $this->name = $name;
        $this->firstname = $firstname;
        $this->order = $order;
        $this->limit = $limit;
        $this->offset = $offset;
    }
}
