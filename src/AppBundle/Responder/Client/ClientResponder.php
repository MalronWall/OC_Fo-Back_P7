<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Responder\Client;

use AppBundle\Domain\Entity\Client;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ClientResponder
{
    /** @var UrlGeneratorInterface */
    private $urlGenerator;
    /** @var SerializerInterface */
    private $serializer;

    /**
     * UserResponder constructor.
     * @param UrlGeneratorInterface $urlGenerator
     * @param SerializerInterface $serializer
     */
    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        SerializerInterface $serializer
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->serializer = $serializer;
    }

    /**
     * @param null $client
     * @param null $error
     * @return Response
     */
    public function showResponse($client = null, $error = null)
    {
        return new Response(
            is_null($client) ?
                (is_null($error) ?
                    null
                    : $error)
                : $this->serializer->serialize($client, 'json', ['groups' => ['client_detail']]),
            is_null($client) ?
                is_null($error) ?
                    Response::HTTP_NO_CONTENT
                    : Response::HTTP_NOT_FOUND
                : Response::HTTP_OK,
            [
                'Content-Type' => 'application/json'
            ]
        );
    }

    /**
     * @param array $client
     * @param null $error
     * @return Response
     */
    public function createResponse(array $client = null, $error = null)
    {
        return new Response(
            is_null($error) ?
                $this->serializer->serialize(
                    $client,
                    'json',
                    [
                        'groups' => [
                            'client_detail',
                            'user_list',
                            'phone_list'
                        ]
                    ]
                )
                : $error,
            is_null($error) ?
                Response::HTTP_CREATED
                : Response::HTTP_NOT_FOUND,
            is_null($error) ?
                [
                    'Content-Type' => 'application/json',
                    "Location" => $this->urlGenerator->generate("client_show",
                        ["id" => $client["datas"][0]["client"]->getId()])
                ]
                :
                [
                    'Content-Type' => 'application/json'
                ]
        );
    }
}
