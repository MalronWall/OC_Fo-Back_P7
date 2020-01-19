<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Domain\Objects;

class JWT
{
    /**
     * @var array
     */
    private $header;
    /**
     * @var array
     */
    private $payload;
    /**
     * @var array
     */
    private $signature;

    /**
     * JWT constructor.
     * @param string $header
     * @param string $payload
     * @param string $signature
     */
    public function __construct(
        string $header,
        string $payload,
        string $signature
    ) {
        $this->header = $header;
        $this->payload = $payload;
        $this->signature = $signature;
    }

    /**
     * @param string $secret
     * @return bool
     */
    public function isValid(string $secret): bool
    {
        // SIGNATURE
        $signature = hash_hmac(
            "sha256",
            $this->header.".".$this->payload,
            $secret,
            true
        );

        $signature = str_replace(
            ['+', '/', '='],
            ['-', '_', ''],
            base64_encode($signature)
        );

        return $this->signature === $signature;
    }

    public function isOnDate(): bool
    {
        return $this->getPayload()->exp >= (new \DateTime())->getTimestamp();
    }

    /**
     * @return string
     */
    public function getJWT()
    {
        return "{$this->header}.{$this->payload}.{$this->signature}";
    }

    /**
     * @return \stdClass
     */
    public function getHeader(): \stdClass
    {
        return json_decode(base64_decode($this->header));
    }

    /**
     * @param array $header
     */
    public function setHeader(array $header): void
    {
        $this->header = $header;
    }

    /**
     * @return \stdClass
     */
    public function getPayload(): \stdClass
    {
        return json_decode(base64_decode($this->payload));
    }

    /**
     * @param array $payload
     */
    public function setPayload(array $payload): void
    {
        $this->payload = $payload;
    }

    /**
     * @return array
     */
    public function getSignature(): array
    {
        return $this->signature;
    }

    /**
     * @param array $signature
     */
    public function setSignature(array $signature): void
    {
        $this->signature = $signature;
    }
}
