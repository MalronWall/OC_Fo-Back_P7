<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Controller\Users;

use AppBundle\Domain\Entity\User;
use AppBundle\Domain\Helpers\Client\DB\ClientDBManager;
use AppBundle\Domain\Helpers\Common\HateoasManager;
use AppBundle\Domain\Helpers\User\DB\UserDBManager;
use AppBundle\Domain\Helpers\User\Validator\UserValidatorHelper;
use AppBundle\Responder\User\UserResponder;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class UserController
{
    /** @var SerializerInterface */
    private $serializer;
    /** @var UserValidatorHelper */
    private $userValidatorHelper;
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
     * @param UserValidatorHelper $userValidatorHelper
     * @param UserDBManager $userDBManager
     * @param ClientDBManager $clientDBManager
     * @param UserResponder $userResponder
     * @param HateoasManager $hateoasManager
     */
    public function __construct(
        SerializerInterface $serializer,
        UserValidatorHelper $userValidatorHelper,
        UserDBManager $userDBManager,
        ClientDBManager $clientDBManager,
        UserResponder $userResponder,
        HateoasManager $hateoasManager
    ) {
        $this->serializer = $serializer;
        $this->userValidatorHelper = $userValidatorHelper;
        $this->userDBManager = $userDBManager;
        $this->clientDBManager = $clientDBManager;
        $this->userResponder = $userResponder;
        $this->hateoasManager = $hateoasManager;
    }

    /**
     * @Route("/api/users", name="user_list", methods={"GET"})
     * @param Request $request
     * @return Response
     * @SWG\Response(
     *     response="200",
     *     description="Return list of users",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="datas",
     *              type="array",
     *              @SWG\Items(
     *                  type="object",
     *                  @SWG\Property(
     *                      property="user",
     *                      type="object",
     *                      ref=@Model(type=User::class)
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
     *     name="name",
     *     in="query",
     *     type="string",
     *     description="'Name of the user'"
     * )
     * @SWG\Parameter(
     *     name="firstname",
     *     in="query",
     *     type="string",
     *     description="'Firstname of the user'"
     * )
     * @SWG\Parameter(
     *     name="order",
     *     in="query",
     *     type="string",
     *     description="'Ascendant (asc) or Descendant (desc) list of users'"
     * )
     * @SWG\Parameter(
     *     name="limit",
     *     in="query",
     *     type="integer",
     *     description="'Limit of the number of user'"
     * )
     * @SWG\Parameter(
     *     name="offset",
     *     in="query",
     *     type="integer",
     *     description="'First user called in db'"
     * )
     * @SWG\Tag(name="User")
     * @Security(name="Bearer")
     */
    public function listAction(Request $request)
    {
        $errors = null;
        $datas = null;
        try {
            $dto = $this->userValidatorHelper->listUserParameterValidate($request->query);
            $results = $this->hateoasManager->buildHateoas(
                $this->userDBManager->listUser($dto, $request->getSession()->get('JWT')),
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
     * @param Request $request
     * @param $id
     * @return Response
     * @SWG\Response(
     *     response="200",
     *     description="Return the details of a user",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="datas",
     *              type="array",
     *              @SWG\Items(
     *                  type="object",
     *                  @SWG\Property(
     *                      property="user",
     *                      type="object",
     *                      ref=@Model(type=User::class)
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
     * @SWG\Tag(name="User")
     * @Security(name="Bearer")
     */
    public function showAction(Request $request, $id)
    {
        $error = null;
        $datas = null;
        try {
            $user = $this->hateoasManager->buildHateoas(
                $this->userDBManager->existUser($id, $request->getSession()->get('JWT')),
                "user",
                [HateoasManager::LIST, HateoasManager::CREATE, HateoasManager::DELETE]
            );
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
     * @SWG\Response(
     *     response="201",
     *     description="Create new user",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="datas",
     *              type="array",
     *              @SWG\Items(
     *                  type="object",
     *                  @SWG\Property(
     *                      property="user",
     *                      type="object",
     *                      ref=@Model(type=User::class)
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
     * @SWG\Tag(name="User")
     * @Security(name="Bearer")
     */
    public function createAction(Request $request)
    {
        $error = null;
        $user = null;
        try {
            $dto = $this->userValidatorHelper->createUserParameterValidate($request->getContent());
            $dto->client = $this->clientDBManager->existClientById($request->getSession()->get('JWT'));
            $user = $this->hateoasManager->buildHateoas(
                $this->userDBManager->createUser($dto),
                "user",
                [HateoasManager::LIST, HateoasManager::SHOW, HateoasManager::DELETE]
            );
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        return $this->userResponder->createResponse($user, $error);
    }

    /**
     * @Route("/api/users/{id}", name="user_delete", methods={"DELETE"})
     * @param Request $request
     * @param $id
     * @return Response
     * @SWG\Response(
     *     response="204",
     *     description="Delete user"
     * )
     * @SWG\Tag(name="User")
     * @Security(name="Bearer")
     */
    public function deleteAction(Request $request, $id)
    {
        $error = null;
        $datas = null;
        try {
            $user = $this->userDBManager->existUser($id, $request->getSession()->get('JWT'))["datas"][0];
            $this->userDBManager->delete($user);
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        return $this->userResponder->deleteResponse($error);
    }
}
