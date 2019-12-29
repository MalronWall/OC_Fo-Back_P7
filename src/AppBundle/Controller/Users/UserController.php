<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Controller\Users;

use AppBundle\Domain\DTO\Users\CreateUserDTO;
use AppBundle\Domain\Helpers\Client\DB\ClientDBManager;
use AppBundle\Domain\Helpers\User\DB\UserDBManager;
use AppBundle\Domain\Helpers\User\Validator\UserValidatorHelper;
use AppBundle\Domain\Representation\DefaultRepresentation;
use AppBundle\Responder\User\UserResponder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidatorException;

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
    /** @var DefaultRepresentation */
    private $defaultRepresentation;
    /** @var UserDBManager */
    private $userDBManager;
    /** @var UserResponder */
    private $userResponder;
    /** @var ClientDBManager */
    private $clientDBManager;

    /**
     * UserController constructor.
     * @param SerializerInterface $serializer
     * @param UrlGeneratorInterface $urlGenerator
     * @param UserValidatorHelper $userValidatorHelper
     * @param EntityManagerInterface $entityManager
     * @param UserDBManager $userDBManager
     * @param ClientDBManager $clientDBManager
     * @param DefaultRepresentation $defaultRepresentation
     * @param UserResponder $userResponder
     */
    public function __construct(
        SerializerInterface $serializer,
        UrlGeneratorInterface $urlGenerator,
        UserValidatorHelper $userValidatorHelper,
        EntityManagerInterface $entityManager,
        UserDBManager $userDBManager,
        ClientDBManager $clientDBManager,
        DefaultRepresentation $defaultRepresentation,
        UserResponder $userResponder
    ) {
        $this->serializer = $serializer;
        $this->urlGenerator = $urlGenerator;
        $this->userValidatorHelper = $userValidatorHelper;
        $this->entityManager = $entityManager;
        $this->userDBManager = $userDBManager;
        $this->clientDBManager = $clientDBManager;
        $this->defaultRepresentation = $defaultRepresentation;
        $this->userResponder = $userResponder;
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
            $usersWithPager = $this->userDBManager->listUser($dto);
            $defaultDisplay = $this->defaultRepresentation->defaultDisplay($usersWithPager);
            $datas = $this->serializer->serialize($defaultDisplay, 'json', ['groups' => ['user_list', 'client_list']]);
        } catch (ValidatorException $e) {
            $errors = $e->getMessage();
        }

        return $this->userResponder->listResponse($datas, $errors);
    }

    /**
     * @Route("/api/users/{id}", name="user_show", methods={"GET"})
     * @param $id
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function showAction($id)
    {
        $error = null;
        $datas = null;
        try {
            $user = $this->userDBManager->existUser($id);
            $datas = $this->serializer->serialize($user, 'json', ['groups' => ['user_detail', 'client_list']]);
        } catch (NotFoundHttpException $e) {
            $error = $e->getMessage();
        }

        return $this->userResponder->showResponse($datas, $error);
    }

    /**
     * @Route("/api/users", name="user_create", methods={"POST"})
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function createAction(Request $request)
    {
        $error = null;
        $user = null;
        try {
            $dto = $this->userValidatorHelper->createUserParameterValidate($request->getContent());
            $dto->client = $this->clientDBManager->existClient($dto->idClient);
            $user = $this->userDBManager->createUser($dto);
        } catch (ValidatorException $e) {
            $error = $e->getMessage();
        } catch (NotFoundHttpException $e) {
            $error = $e->getMessage();
        }

        return $this->userResponder->createResponse($user, $error);
    }

    /**
     * @Route("/api/users/{id}", name="user_delete", methods={"DELETE"})
     * @param $id
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function deleteAction($id)
    {
        $error = null;
        $datas = null;
        try {
            $user = $this->userDBManager->existUser($id);
            $this->userDBManager->delete($user);
        } catch (NotFoundHttpException $e) {
            $error = $e->getMessage();
        }

        return $this->userResponder->deleteResponse($error);
    }
}