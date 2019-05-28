<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Controller\Users;

use AppBundle\AppBundle;
use AppBundle\Domain\Entity\ASupprimer;
use AppBundle\Domain\Entity\User;
use AppBundle\Helper\Users\UserHelper;
use AppBundle\Representation\DefaultRepresentation;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class UserController
{
    /** @var SerializerInterface */
    private $serializer;
    /** @var UserHelper */
    private $helper;

    public function __construct(
        SerializerInterface $serializer,
        UserHelper $helper
    ) {
        $this->serializer = $serializer;
        $this->helper = $helper;
    }

    /**
     * @Route("/api/users", name="user_list", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function listAction(Request $request)
    {
        $errors = null;
        $datas = null;
        try {
            $users = $this->helper->listUser(
                $request->get('name'),
                $request->get('firstname'),
                $request->get('order'),
                $request->get('limit'),
                $request->get('offset')
            );
            $pager = new DefaultRepresentation($users);
            $datas = $this->serializer->serialize($pager, 'json');
            //$datas = $this->serializer->serialize($users, 'json', ['groups' => ['user_list']]);
        } catch (\Exception $e) {
            $errors = $this->serializer->serialize($e->getMessage(), 'json');
        }

        return new Response(
            is_null($datas) ? (is_null($errors) ? null : $errors) : $datas,
            is_null($datas) ?
                is_null($errors) ? Response::HTTP_NO_CONTENT : Response::HTTP_BAD_REQUEST
                : Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * @Route("/api/users/{id}", name="user_show", methods={"GET"})
     * @param User $user
     * @return Response
     */
    public function showAction(User $user)
    {
        $data = $this->serializer->serialize($user, 'json', ['groups' => ['user_detail']]);

        return new Response(
            !is_null($data) ? $data : null,
            !is_null($data) ? Response::HTTP_OK : Response::HTTP_NO_CONTENT,
            [
                'Content-Type' => 'application/json'
            ]
        );
    }

    /**
     * @Route("/api/users", name="user_create", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function createAction(Request $request)
    {
        $data = $request->getContent();
        /** @var User $user */
        $user = $this->serializer->deserialize($data, 'AppBundle\Domain\Entity\User', 'json');
        $this->helper->createUser($user);

        return new Response('', Response::HTTP_CREATED);
    }

    /**
     * @Route("/api/users/{id}", name="user_delete", methods={"DELETE"})
     * @param User $user
     * @return Response
     */
    public function deleteAction(User $user)
    {
        //$user = $this->serializer->deserialize($user, 'AppBundle\Domain\Entity\User', 'json');
        $this->helper->deleteUser($user);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
