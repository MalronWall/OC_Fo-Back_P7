<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Responder\Phone;

use AppBundle\Domain\Entity\Client;
use AppBundle\Domain\Entity\Phone;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;

class PhoneResponder
{
    /** @var UrlGeneratorInterface */
    private $urlGenerator;
    /** @var SerializerInterface */
    private $serializer;

    /**
     * PhoneResponder constructor.
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
     * @param null $datas
     * @param null $errors
     * @return Response
     */
    public function listResponse($datas = null, $errors = null)
    {
        return new Response(
            is_null($datas) ?
                (is_null($errors) ?
                    null
                    : $errors)
                : $datas,
            is_null($datas) ?
                is_null($errors) ?
                    Response::HTTP_NO_CONTENT
                    : Response::HTTP_BAD_REQUEST
                : Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * @param null $datas
     * @param null $error
     * @return Response
     */
    public function showResponse($datas = null, $error = null)
    {
        return new Response(
            is_null($datas) ?
                (is_null($error) ?
                    null
                    : $error)
                : $datas,
            is_null($datas) ?
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
     * @param Phone|null $phone
     * @param null $error
     * @return Response
     */
    public function createResponse(array $phone = null, $error = null)
    {
        return new Response(
            is_null($error) ?
                $this->serializer->serialize($phone, 'json', ['groups' => ['phone_detail', 'client_list']])
                : $error,
            is_null($error) ?
                Response::HTTP_CREATED
                : Response::HTTP_NOT_FOUND,
            is_null($error) ?
                [
                    'Content-Type' => 'application/json',
                    "Location" => $this->urlGenerator->generate("phone_show", ["id" => $phone["datas"][0]["phone"]->getId()])
                ]
                :
                [
                    'Content-Type' => 'application/json'
                ]
        );
    }

    /**
     * @param null $error
     * @return Response
     */
    public function deleteResponse($error = null)
    {
        return new Response(
            is_null($error) ?
                null
                : $error,
            is_null($error) ?
                Response::HTTP_NO_CONTENT
                : Response::HTTP_NOT_FOUND,
            [
                'Content-Type' => 'application/json'
            ]
        );
    }
}
