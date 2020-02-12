<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Controller\Phones;

use AppBundle\Domain\Entity\Phone;
use AppBundle\Domain\Helpers\Client\DB\ClientDBManager;
use AppBundle\Domain\Helpers\Common\HateoasManager;
use AppBundle\Domain\Helpers\Phone\DB\PhoneDBManager;
use AppBundle\Domain\Helpers\Phone\Validator\PhoneValidatorHelper;
use AppBundle\Responder\Phone\PhoneResponder;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
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
     * @SWG\Response(
     *     response="200",
     *     description="Return list of phones",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="datas",
     *              type="array",
     *              @SWG\Items(
     *                  type="object",
     *                  @SWG\Property(
     *                      property="phone",
     *                      type="object",
     *                      ref=@Model(type=Phone::class)
     *                  ),
     *                  @SWG\Property(
     *                      property="links",
     *                      type="array",
     *                      @SWG\Items(
     *                          type="object",
     *                          @SWG\Property(property="url", type="string"),
     *                          @SWG\Property(property="method", type="string"),
     *                          @SWG\Property(property="returnType", type="string")
     *                      )
     *                  )
     *              )
     *          ),
     *          @SWG\Property(
     *              property="metas",
     *              type="object",
     *              @SWG\Property(property="nbTotalOfItems", type="integer"),
     *              @SWG\Property(property="currentPage", type="integer"),
     *              @SWG\Property(property="totalPages", type="integer"),
     *          )
     *      )
     * )
     * @SWG\Parameter(
     *     name="brand",
     *     in="query",
     *     type="string",
     *     description="'Brand of the phone'"
     * )
     * @SWG\Parameter(
     *     name="model",
     *     in="query",
     *     type="string",
     *     description="'Model of the phone'"
     * )
     * @SWG\Parameter(
     *     name="os",
     *     in="query",
     *     type="string",
     *     description="'OS of the phone'"
     * )
     * @SWG\Parameter(
     *     name="order",
     *     in="query",
     *     type="string",
     *     description="'Ascendant (asc) or Descendant (desc) list of phones'"
     * )
     * @SWG\Parameter(
     *     name="limit",
     *     in="query",
     *     type="integer",
     *     description="'Limit of the number of phone'"
     * )
     * @SWG\Parameter(
     *     name="offset",
     *     in="query",
     *     type="integer",
     *     description="'First phone called in db'"
     * )
     * @SWG\Tag(name="Phone")
     * @Security(name="Bearer")
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
                [HateoasManager::SHOW, HateoasManager::CREATE, HateoasManager::DELETE]
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
     * @SWG\Response(
     *     response="200",
     *     description="Return the details of a phone",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="datas",
     *              type="array",
     *              @SWG\Items(
     *                  type="object",
     *                  @SWG\Property(
     *                      property="phone",
     *                      type="object",
     *                      ref=@Model(type=Phone::class)
     *                  ),
     *                  @SWG\Property(
     *                      property="links",
     *                      type="array",
     *                      @SWG\Items(
     *                          type="object",
     *                          @SWG\Property(property="url", type="string"),
     *                          @SWG\Property(property="method", type="string"),
     *                          @SWG\Property(property="returnType", type="string")
     *                      )
     *                  )
     *              )
     *          )
     *      )
     * )
     * @SWG\Tag(name="Phone")
     * @Security(name="Bearer")
     */
    public function showAction($id)
    {
        $error = null;
        $datas = null;
        try {
            $phone = $this->hateoasManager->buildHateoas(
                $this->phoneDBManager->existPhone($id),
                "phone",
                [HateoasManager::LIST, HateoasManager::CREATE, HateoasManager::DELETE]
            );
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
     * @SWG\Response(
     *     response="201",
     *     description="Create phone",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="datas",
     *              type="array",
     *              @SWG\Items(
     *                  type="object",
     *                  @SWG\Property(
     *                      property="phone",
     *                      type="object",
     *                      ref=@Model(type=Phone::class)
     *                  ),
     *                  @SWG\Property(
     *                      property="links",
     *                      type="array",
     *                      @SWG\Items(
     *                          type="object",
     *                          @SWG\Property(property="url", type="string"),
     *                          @SWG\Property(property="method", type="string"),
     *                          @SWG\Property(property="returnType", type="string")
     *                      )
     *                  )
     *              )
     *          )
     *      )
     * )
     * @SWG\Tag(name="Phone")
     * @Security(name="Bearer")
     */
    public function createAction(Request $request)
    {
        $error = null;
        $phone = null;
        try {
            $dto = $this->phoneValidatorHelper->createPhoneParameterValidate(json_decode($request->getContent(), true));
            $phone = $this->hateoasManager->buildHateoas(
                $this->phoneDBManager->createPhone($dto),
                "phone",
                [HateoasManager::LIST, HateoasManager::SHOW, HateoasManager::DELETE]
            );
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        return $this->phoneResponder->createResponse($phone, $error);
    }

    /**
     * @Route("/api/phones/{id}", name="phone_delete", methods={"DELETE"})
     * @param $id
     * @return Response
     * @SWG\Response(
     *     response="204",
     *     description="Delete phone"
     * )
     * @SWG\Tag(name="Phone")
     * @Security(name="Bearer")
     */
    public function deleteAction($id)
    {
        $error = null;
        $datas = null;
        try {
            $phone = $this->phoneDBManager->existPhone($id)["datas"][0];
            $this->phoneDBManager->delete($phone);
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        return $this->phoneResponder->deleteResponse($error);
    }
}
