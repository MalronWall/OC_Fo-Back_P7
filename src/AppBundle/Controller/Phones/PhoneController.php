<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Controller\Phones;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class PhoneController
{
//    /** @var SerializerInterface */
//    protected $serializer;
//
//    public function __construct(
//        SerializerInterface $serializer
//    ) {
//        $this->serializer = $serializer;
//    }
//
//    /**
//     * @Route("/api/phones", name="phone_list", methods={"GET"})
//     */
//    public function listAction()
//    {
//        // TODO $phones = $this->entityManager->getRepository(Phone::class)->findAll();
//        $phones = null;
//        $datas = count($phones) > 0 ?
//            $this->serializer->serialize($phones, 'json') :
//            null;
//
//        return new Response(
//            !is_null($datas) ? $datas : null,
//            !is_null($datas) ? Response::HTTP_OK : Response::HTTP_NO_CONTENT,
//            [
//                'Content-Type' => 'application/json'
//            ]
//        );
//    }
}
