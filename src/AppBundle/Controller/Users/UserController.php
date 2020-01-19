<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Controller\Users;

use AppBundle\Domain\Helpers\Client\DB\ClientDBManager;
use AppBundle\Domain\Helpers\Common\HateoasManager;
use AppBundle\Domain\Helpers\User\DB\UserDBManager;
use AppBundle\Domain\Helpers\User\Validator\UserValidatorHelper;
use AppBundle\Responder\User\UserResponder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;

class UserController
{
    /** @var SerializerInterface */
    private $serializer;
    /** @var UrlGeneratorInterface */
    private $urlGenerator;
    /** @var UserValidatorHelper */
    private $userValidatorHelper;
    /** @var EntityManagerInterface */
    private $entityManager;
    /** @var UserDBManager */
    private $userDBManager;
    /** @var UserResponder */
    private $userResponder;
    /** @var ClientDBManager */
    private $clientDBManager;
    /**
     * @var HateoasManager
     */
    private $hateoasManager;

    /**
     * UserController constructor.
     * @param SerializerInterface $serializer
     * @param UrlGeneratorInterface $urlGenerator
     * @param UserValidatorHelper $userValidatorHelper
     * @param EntityManagerInterface $entityManager
     * @param UserDBManager $userDBManager
     * @param ClientDBManager $clientDBManager
     * @param UserResponder $userResponder
     * @param HateoasManager $hateoasManager
     */
    public function __construct(
        SerializerInterface $serializer,
        UrlGeneratorInterface $urlGenerator,
        UserValidatorHelper $userValidatorHelper,
        EntityManagerInterface $entityManager,
        UserDBManager $userDBManager,
        ClientDBManager $clientDBManager,
        UserResponder $userResponder,
        HateoasManager $hateoasManager
    ) {
        $this->serializer = $serializer;
        $this->urlGenerator = $urlGenerator;
        $this->userValidatorHelper = $userValidatorHelper;
        $this->entityManager = $entityManager;
        $this->userDBManager = $userDBManager;
        $this->clientDBManager = $clientDBManager;
        $this->userResponder = $userResponder;
        $this->hateoasManager = $hateoasManager;
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
            $dto = $this->userValidatorHelper->listUserParameterValidate($request->query);
            $results = $this->hateoasManager->buildHateoas(
                $this->userDBManager->listUser($dto),
                "user",
                [HateoasManager::SHOW, HateoasManager::CREATE, HateoasManager::DELETE]
            );
            $datas = $this->serializer->serialize(
                $results,
                'json',
                ['groups' => ['user_list', 'client_list']]
            );
        } catch (\Exception $e) {
            $errors = $e->getMessage();
        }

        return $this->userResponder->listResponse($datas, $errors);
    }

    /**
     * @Route("/api/users/{id}", name="user_show", methods={"GET"})
     * @param $id
     * @return Response
     */
    public function showAction($id)
    {
        $error = null;
        $datas = null;
        try {
            $user = $this->userDBManager->existUser($id);
            $datas = $this->serializer->serialize($user, 'json', ['groups' => ['user_detail', 'client_list']]);
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        return $this->userResponder->showResponse($datas, $error);
    }

    /**
     * @Route("/api/users", name="user_create", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function createAction(Request $request)
    {
        $error = null;
        $user = null;
        try {
            $dto = $this->userValidatorHelper->createUserParameterValidate($request->getContent());
            $dto->client = $this->clientDBManager->existClient($dto->idClient);
            $user = $this->userDBManager->createUser($dto);
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        return $this->userResponder->createResponse($user, $error);
    }

    /**
     * @Route("/api/users/{id}", name="user_delete", methods={"DELETE"})
     * @param $id
     * @return Response
     */
    public function deleteAction($id)
    {
        $error = null;
        $datas = null;
        try {
            $user = $this->userDBManager->existUser($id);
            $this->userDBManager->delete($user);
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        return $this->userResponder->deleteResponse($error);
    }
}
