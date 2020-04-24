<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Responder\Security;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ApiResponder
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
     * @param null $datas
     * @param null $error
     * @return Response
     */
    public function response($datas = null, $error = null)
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
}
