<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Controller\Phones;

use AppBundle\Domain\Helpers\Client\DB\ClientDBManager;
use AppBundle\Domain\Helpers\Phone\DB\PhoneDBManager;
use AppBundle\Domain\Helpers\Phone\Validator\PhoneValidatorHelper;
use AppBundle\Domain\Representation\DefaultRepresentation;
use AppBundle\Responder\Phone\PhoneResponder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidatorException;

class PhoneController
{
    /** @var PhoneValidatorHelper */
    private $phoneValidatorHelper;
    /** @var PhoneDBManager */
    private $phoneDBManager;
    /** @var DefaultRepresentation */
    private $defaultRepresentation;
    /** @var SerializerInterface */
    private $serializer;
    /** @var PhoneResponder */
    private $phoneResponder;
    /** @var ClientDBManager */
    private $clientDBManager;

    /**
     * PhoneController constructor.
     * @param PhoneValidatorHelper $phoneValidatorHelper
     * @param PhoneDBManager $phoneDBManager
     * @param ClientDBManager $clientDBManager
     * @param DefaultRepresentation $defaultRepresentation
     * @param SerializerInterface $serializer
     * @param PhoneResponder $phoneResponder
     */
    public function __construct(
        PhoneValidatorHelper $phoneValidatorHelper,
        PhoneDBManager $phoneDBManager,
        ClientDBManager $clientDBManager,
        DefaultRepresentation $defaultRepresentation,
        SerializerInterface $serializer,
        PhoneResponder $phoneResponder
    ) {
        $this->phoneValidatorHelper = $phoneValidatorHelper;
        $this->phoneDBManager = $phoneDBManager;
        $this->clientDBManager = $clientDBManager;
        $this->defaultRepresentation = $defaultRepresentation;
        $this->serializer = $serializer;
        $this->phoneResponder = $phoneResponder;
    }

    /**
     * @Route("/api/phones", name="phone_list", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function listAction(Request $request)
    {
        $errors = null;
        $datas = null;
        try {
            $dto = $this->phoneValidatorHelper->listPhoneParameterValidate($request->query);
            $phonesWithPager = $this->phoneDBManager->listPhone($dto);
            $defaultDisplay = $this->defaultRepresentation->defaultDisplay($phonesWithPager);
            $datas = $this->serializer->serialize($defaultDisplay, 'json', ['groups' => ['phone_list', 'client_list']]);
        } catch (ValidatorException $e) {
            $errors = $e->getMessage();
        }

        return $this->phoneResponder->listResponse($datas, $errors);
    }

    /**
     * @Route("/api/phones/{id}", name="phone_show", methods={"GET"})
     * @param $id
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function showAction($id)
    {
        $error = null;
        $datas = null;
        try {
            $phone = $this->phoneDBManager->existPhone($id);
            $datas = $this->serializer->serialize($phone, 'json', ['groups' => ['phone_detail', 'client_list']]);
        } catch (NotFoundHttpException $e) {
            $error = $e->getMessage();
        }

        return $this->phoneResponder->showResponse($datas, $error);
    }

    /**
     * @Route("/api/phones", name="phone_create", methods={"POST"})
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function createAction(Request $request)
    {
        $error = null;
        $phone = null;
        try {
            $dto = $this->phoneValidatorHelper->createPhoneParameterValidate(json_decode($request->getContent(), true));
            $dto->client = $this->clientDBManager->existClient($dto->idClient);
            $phone = $this->phoneDBManager->createPhone($dto);
        } catch (ValidatorException $e) {
            $error = $e->getMessage();
        }

        return $this->phoneResponder->createResponse($phone, $error);
    }
}
