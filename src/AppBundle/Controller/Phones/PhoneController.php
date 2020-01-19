<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Controller\Phones;

use AppBundle\Domain\Helpers\Client\DB\ClientDBManager;
use AppBundle\Domain\Helpers\Common\HateoasManager;
use AppBundle\Domain\Helpers\Phone\DB\PhoneDBManager;
use AppBundle\Domain\Helpers\Phone\Validator\PhoneValidatorHelper;
use AppBundle\Responder\Phone\PhoneResponder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class PhoneController
{
    /** @var PhoneValidatorHelper */
    private $phoneValidatorHelper;
    /** @var PhoneDBManager */
    private $phoneDBManager;
    /** @var SerializerInterface */
    private $serializer;
    /** @var PhoneResponder */
    private $phoneResponder;
    /** @var ClientDBManager */
    private $clientDBManager;
    /**
     * @var HateoasManager
     */
    private $hateoasManager;

    /**
     * PhoneController constructor.
     * @param PhoneValidatorHelper $phoneValidatorHelper
     * @param PhoneDBManager $phoneDBManager
     * @param ClientDBManager $clientDBManager
     * @param SerializerInterface $serializer
     * @param PhoneResponder $phoneResponder
     * @param HateoasManager $hateoasManager
     */
    public function __construct(
        PhoneValidatorHelper $phoneValidatorHelper,
        PhoneDBManager $phoneDBManager,
        ClientDBManager $clientDBManager,
        SerializerInterface $serializer,
        PhoneResponder $phoneResponder,
        HateoasManager $hateoasManager
    ) {
        $this->phoneValidatorHelper = $phoneValidatorHelper;
        $this->phoneDBManager = $phoneDBManager;
        $this->clientDBManager = $clientDBManager;
        $this->serializer = $serializer;
        $this->phoneResponder = $phoneResponder;
        $this->hateoasManager = $hateoasManager;
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
            $results = $this->hateoasManager->buildHateoas(
                $this->phoneDBManager->listPhone($dto),
                "phone",
                [HateoasManager::SHOW]
            );
            $datas = $this->serializer->serialize(
                $results,
                'json',
                ['groups' => ['phone_list', 'client_list']]
            );
        } catch (\Exception $e) {
            $errors = $e->getMessage();
        }

        return $this->phoneResponder->listResponse($datas, $errors);
    }

    /**
     * @Route("/api/phones/{id}", name="phone_show", methods={"GET"})
     * @param $id
     * @return Response
     */
    public function showAction($id)
    {
        $error = null;
        $datas = null;
        try {
            $phone = $this->phoneDBManager->existPhone($id);
            $datas = $this->serializer->serialize($phone, 'json', ['groups' => ['phone_detail', 'client_list']]);
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        return $this->phoneResponder->showResponse($datas, $error);
    }

    /**
     * @Route("/api/phones", name="phone_create", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function createAction(Request $request)
    {
        $error = null;
        $phone = null;
        try {
            $dto = $this->phoneValidatorHelper->createPhoneParameterValidate(json_decode($request->getContent(), true));
            $phone = $this->phoneDBManager->createPhone($dto);
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        return $this->phoneResponder->createResponse($phone, $error);
    }
}
