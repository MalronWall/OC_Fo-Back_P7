<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Domain\Entity;

use Ramsey\Uuid\UuidInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Domain\Repository\PhoneRepository")
 * @ORM\Table(name="phone")
 */
class Phone
{
    /**
     * @var UuidInterface
     *
     * @Groups({"phone_list", "client_detail"})
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
     * @Groups({"phone_list", "phone_detail", "client_detail"})
     *
     * @ORM\Column(type="string")
     */
    private $brand;
    /**
     * @var string
     *
     * @Groups({"phone_list", "phone_detail", "client_detail"})
     *
     * @ORM\Column(type="string")
     */
    private $model;
    /**
     * @var string
     *
     * @Groups({"phone_detail"})
     *
     * @ORM\Column(type="string")
     */
    private $os;
    /**
     * @var float
     *
     * @Groups({"phone_detail"})
     *
     * @ORM\Column(type="float")
     */
    private $price;
    /**
     * @var string
     *
     * @Groups({"phone_detail"})
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $cpu;
    /**
     * @var string
     *
     * @Groups({"phone_detail"})
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $gpu;
    /**
     * @var string
     *
     * @Groups({"phone_detail"})
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $ram;
    /**
     * @var string
     *
     * @Groups({"phone_detail"})
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $memory;
    /**
     * @var string
     *
     * @Groups({"phone_detail"})
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $dimensions;
    /**
     * @var string
     *
     * @Groups({"phone_detail"})
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $weight;
    /**
     * @var string
     *
     * @Groups({"phone_detail"})
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $resolution;
    /**
     * @var string
     *
     * @Groups({"phone_detail"})
     *
     * @ORM\Column(type="string", name="mainCamera", nullable=true)
     */
    private $mainCamera;
    /**
     * @var string
     *
     * @Groups({"phone_detail"})
     *
     * @ORM\Column(type="string", name="selfieCamera", nullable=true)
     */
    private $selfieCamera;
    /**
     * @var string
     *
     * @Groups({"phone_detail"})
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $sound;
    /**
     * @var string
     *
     * @Groups({"phone_detail"})
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $battery;
    /**
     * @var string
     *
     * @Groups({"phone_detail"})
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $colors;
    /**
     * @var Client
     *
     * @Groups({"phone_list", "phone_detail"})
     *
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="phones")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     */
    private $client;

    /**
     * Phones constructor.
     * @param string $brand
     * @param string $model
     * @param string $os
     * @param float $price
     * @param string $cpu
     * @param string $gpu
     * @param string $ram
     * @param string $memory
     * @param string $dimensions
     * @param string $weight
     * @param string $resolution
     * @param string $mainCamera
     * @param string $selfieCamera
     * @param string $sound
     * @param string $battery
     * @param string $colors
     * @param Client|null $client
     */
    public function __construct(
        string $brand,
        string $model,
        string $os,
        float $price,
        Client $client,
        string $cpu = null,
        string $gpu = null,
        string $ram = null,
        string $memory = null,
        string $dimensions = null,
        string $weight = null,
        string $resolution = null,
        string $mainCamera = null,
        string $selfieCamera = null,
        string $sound = null,
        string $battery = null,
        string $colors = null
    ) {
        $this->brand = $brand;
        $this->model = $model;
        $this->os = $os;
        $this->price = $price;
        $this->client = $client;
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
    public function getBrand(): string
    {
        return $this->brand;
    }

    /**
     * @return string
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * @return string
     */
    public function getOs(): string
    {
        return $this->os;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @return string
     */
    public function getCpu(): string
    {
        return $this->cpu;
    }

    /**
     * @return string
     */
    public function getGpu(): string
    {
        return $this->gpu;
    }

    /**
     * @return string
     */
    public function getRam(): string
    {
        return $this->ram;
    }

    /**
     * @return string
     */
    public function getMemory(): string
    {
        return $this->memory;
    }

    /**
     * @return string
     */
    public function getDimensions(): string
    {
        return $this->dimensions;
    }

    /**
     * @return string
     */
    public function getWeight(): string
    {
        return $this->weight;
    }

    /**
     * @return string
     */
    public function getResolution(): string
    {
        return $this->resolution;
    }

    /**
     * @return string
     */
    public function getMainCamera(): string
    {
        return $this->mainCamera;
    }

    /**
     * @return string
     */
    public function getSelfieCamera(): string
    {
        return $this->selfieCamera;
    }

    /**
     * @return string
     */
    public function getSound(): string
    {
        return $this->sound;
    }

    /**
     * @return string
     */
    public function getBattery(): string
    {
        return $this->battery;
    }

    /**
     * @return string
     */
    public function getColors(): string
    {
        return $this->colors;
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }
}
