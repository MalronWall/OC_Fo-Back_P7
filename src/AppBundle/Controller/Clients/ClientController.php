<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Controller\Clients;

use AppBundle\Domain\Helpers\Client\DB\ClientDBManager;
use AppBundle\Domain\Helpers\Client\Validator\ClientValidatorHelper;
use AppBundle\Domain\Helpers\Common\HateoasManager;
use AppBundle\Responder\Client\ClientResponder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ClientController
{
    /** @var SerializerInterface */
    private $serializer;
    /** @var ClientValidatorHelper */
    private $clientValidatorHelper;
    /** @var ClientDBManager */
    private $clientDBManager;
    /** @var ClientResponder */
    private $clientResponder;
    /**
     * @var HateoasManager
     */
    private $hateoasManager;

    /**
     * ClientController constructor.
     * @param SerializerInterface $serializer
     * @param ClientValidatorHelper $clientValidatorHelper
     * @param ClientDBManager $clientDBManager
     * @param ClientResponder $clientResponder
     * @param HateoasManager $hateoasManager
     */
    public function __construct(
        SerializerInterface $serializer,
        ClientValidatorHelper $clientValidatorHelper,
        ClientDBManager $clientDBManager,
        ClientResponder $clientResponder,
        HateoasManager $hateoasManager
    ) {
        $this->serializer = $serializer;
        $this->clientValidatorHelper = $clientValidatorHelper;
        $this->clientDBManager = $clientDBManager;
        $this->clientResponder = $clientResponder;
        $this->hateoasManager = $hateoasManager;
    }

    /**
     * @Route("/api/clients/{id}", name="client_show", methods={"GET"})
     * @param $id
     * @return Response
     */
    public function showAction($id)
    {
        $error = null;
        $datas = null;
        try {
            $client = $this->clientDBManager->existClientById($id);
            $datas = $this->hateoasManager->buildHateoas(
                $client,
                "client",
                []
            );
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        return $this->clientResponder->showResponse($datas, $error);
    }

    /**
     * @Route("/api/clients", name="client_create", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function createAction(Request $request)
    {
        $error = null;
        $client = null;
        try {
            $dto =
                $this->clientValidatorHelper->createClientParameterValidate(json_decode($request->getContent(), true));
            $this->clientDBManager->existClientByUsername($dto->username);
            $client = $this->hateoasManager->buildHateoas(
                $this->clientDBManager->createClient($dto),
                "client",
                [HateoasManager::SHOW]
            );
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        return $this->clientResponder->createResponse($client, $error);
    }
}
