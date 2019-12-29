<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Domain\DTO\Phones;

use Symfony\Component\Validator\Constraints as Assert;

class ListPhoneDTO
{
    /**
     * @var null|string
     */
    public $brand;
    /**
     * @var null|string
     */
    public $model;
    /**
     * @var null|string
     */
    public $os;
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
     * ListPhoneDTO constructor.
     * @param null|string $brand
     * @param null|string $model
     * @param null|string $os
     * @param null|string $order
     * @param null|string $limit
     * @param null|string $offset
     */
    public function __construct(
        ?string $brand,
        ?string $model,
        ?string $os,
        ?string $order,
        ?string $limit,
        ?string $offset
    ) {
        $this->brand = $brand;
        $this->model = $model;
        $this->os = $os;
        $this->order = $order;
        $this->limit = $limit;
        $this->offset = $offset;
    }


}
