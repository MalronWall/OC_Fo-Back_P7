<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Domain\Helpers\Security;

use AppBundle\Domain\Entity\Client;
use AppBundle\Domain\Objects\JWT;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class JWTManager
{
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * JWTManager constructor.
     * @param ContainerInterface $container
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        ContainerInterface $container,
        EntityManagerInterface $entityManager
    ) {
        $this->container = $container;
        $this->entityManager = $entityManager;
    }

    /**
     * @param Client $client
     * @return JWT
     * @throws \Exception
     */
    public function build(Client $client): JWT
    {
        // HEADER
        $header = [
            "alg" => "HS256",
            "typ" => "Authentification_JWT",
        ];

        $header = str_replace(
            ['+', '/', '='],
            ['-', '_', ''],
            base64_encode(json_encode($header))
        );

        // PAYLOAD
        $iat = new \DateTime();

        $payload = [
            "sub" => $client->getId()->toString(),
            "name" => $client["datas"][0]->getUsername(),
            "iat" => $iat->getTimestamp(),
            "exp" => $iat->add(new \DateInterval("P1D"))->getTimestamp(),
        ];

        $payload = str_replace(
            ['+', '/', '='],
            ['-', '_', ''],
            base64_encode(json_encode($payload))
        );

        // SIGNATURE
        $signature = hash_hmac(
            "sha256",
            $header.".".$payload,
            $this->container->getParameter("jwt_secret"),
            true
        );

        $signature = str_replace(
            ['+', '/', '='],
            ['-', '_', ''],
            base64_encode($signature)
        );

        return new JWT(
            $header,
            $payload,
            $signature
        );
    }

    /**
     * @param string $jwt
     * @return JWT
     */
    public function buildWithString(string $jwt): JWT
    {
        $jwt = preg_replace("/Bearer /", "", $jwt);
        $jwtTab = preg_split("/\./", $jwt);
        return new JWT($jwtTab[0], $jwtTab[1], $jwtTab[2]);
    }

    /**
     * @param \stdClass $payload
     * @return bool
     */
    public function isValidClient(\stdClass $payload): bool
    {
        /** @var Client $client */
        $client = $this->entityManager->getRepository(Client::class)->findClientById($payload->sub);
        if (is_null($client)) {
            return false;
        }
        return $client["datas"][0]->getUsername() === $payload->name;
    }
}
