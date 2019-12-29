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

class PhoneResponder
{
    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /**
     * PhoneResponder constructor.
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->urlGenerator = $urlGenerator;
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
    public function createResponse(Phone $phone = null, $error = null)
    {
        return new Response(
            is_null($error) ?
                null
                : $error,
            is_null($error) ?
                Response::HTTP_CREATED
                : Response::HTTP_NOT_FOUND,
            is_null($error) ?
                [
                    'Content-Type' => 'application/json',
                    "Location" => $this->urlGenerator->generate("phone_show", ["id" => $phone->getId()])
                ]
                :
                [
                    'Content-Type' => 'application/json'
                ]
        );
    }
}
