<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Domain\DTO\Phones;

use AppBundle\Domain\Entity\Client;
use Symfony\Component\Validator\Constraints as Assert;

class CreatePhoneDTO
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $brand;
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $model;
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $os;
    /**
     * @var float
     * @Assert\NotBlank()
     */
    public $price;
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $idClient;
    /**
     * @var string|null
     */
    public $cpu;
    /**
     * @var string|null
     */
    public $gpu;
    /**
     * @var string|null
     */
    public $ram;
    /**
     * @var string|null
     */
    public $memory;
    /**
     * @var string|null
     */
    public $dimensions;
    /**
     * @var string|null
     */
    public $weight;
    /**
     * @var string|null
     */
    public $resolution;
    /**
     * @var string|null
     */
    public $mainCamera;
    /**
     * @var string|null
     */
    public $selfieCamera;
    /**
     * @var string|null
     */
    public $sound;
    /**
     * @var string|null
     */
    public $battery;
    /**
     * @var string|null
     */
    public $colors;
    /**
     * @var Client
     */
    public $client;

    /**
     * CreatePhoneDTO constructor.
     * @param null|string $brand
     * @param null|string $model
     * @param null|string $os
     * @param float|null $price
     * @param string|null $idClient
     * @param null|string $cpu
     * @param null|string $gpu
     * @param null|string $ram
     * @param null|string $memory
     * @param null|string $dimensions
     * @param null|string $weight
     * @param null|string $resolution
     * @param null|string $mainCamera
     * @param null|string $selfieCamera
     * @param null|string $sound
     * @param null|string $battery
     * @param null|string $colors
     */
    public function __construct(
        ?string $brand,
        ?string $model,
        ?string $os,
        ?float $price,
        ?string $idClient,
        ?string $cpu,
        ?string $gpu,
        ?string $ram,
        ?string $memory,
        ?string $dimensions,
        ?string $weight,
        ?string $resolution,
        ?string $mainCamera,
        ?string $selfieCamera,
        ?string $sound,
        ?string $battery,
        ?string $colors
    ) {
        $this->brand = $brand;
        $this->model = $model;
        $this->os = $os;
        $this->price = $price;
        $this->idClient = $idClient;
        $this->cpu = $cpu;
        $this->gpu = $gpu;
        $this->ram = $ram;
        $this->memory = $memory;
        $this->dimensions = $dimensions;
        $this->weight = $weight;
        $this->resolution = $resolution;
        $this->mainCamera = $mainCamera;
        $this->selfieCamera = $selfieCamera;
        $this->sound = $sound;
        $this->battery = $battery;
        $this->colors = $colors;
    }
}
