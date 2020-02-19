<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace Tests\AppBundle\Controller\Users;

use AppBundle\Controller\Users\UserController;
use AppBundle\Domain\Helpers\Client\DB\ClientDBManager;
use AppBundle\Domain\Helpers\Common\ExceptionManager;
use AppBundle\Domain\Helpers\Common\HateoasManager;
use AppBundle\Domain\Helpers\User\DB\UserDBManager;
use AppBundle\Domain\Helpers\User\Validator\UserValidatorHelper;
use AppBundle\Domain\Repository\UserRepository;
use AppBundle\Responder\User\UserResponder;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Tests\AppBundle\Fixtures\Users;

class UserControllerTest extends KernelTestCase
{
    /** @var UserController */
    private $userController;
    /** @var SerializerInterface|MockObject */
    private $serializer;
    /** @var UserValidatorHelper */
    private $userValidatorHelper;
    /** @var UserDBManager */
    private $userDBManager;
    /** @var ClientDBManager */
    private $clientDBManager;
    /** @var UserResponder */
    private $userResponder;
    /** @var HateoasManager|MockObject */
    private $hateoasManager;
    /** @var ValidatorInterface|MockObject */
    private $validatorInterface;
    /** @var UserRepository|MockObject */
    private $userRepository;

    use Users;

    public function setUp()
    {
        $this->buildUsers();

        $kernel = self::bootKernel();

        $this->serializer = $kernel->getContainer()->get("serializer");

        /** @var ValidatorInterface $validatorInterface */
        $this->validatorInterface = $this->createMock(ValidatorInterface::class);

        /** @var ExceptionManager $exceptionManager */
        $exceptionManager = new ExceptionManager();

        $this->userValidatorHelper =
            new UserValidatorHelper($this->validatorInterface, $exceptionManager, $this->serializer);

        /** @var UserRepository $userRepository *
         * /** @var UserRepository|MockObject userRepository
         */
        $this->userRepository = $this->createMock(UserRepository::class);

        /** @var EntityManagerInterface|MockObject $entityManager */
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method("getRepository")
                      ->willReturn($this->userRepository);

        $this->userDBManager = new UserDBManager($entityManager, $exceptionManager);

        /** @var EncoderFactoryInterface $encoderFactory */
        $encoderFactory = $this->createMock(EncoderFactoryInterface::class);

        $this->clientDBManager = new ClientDBManager($entityManager, $exceptionManager, $encoderFactory);

        /** @var UrlGeneratorInterface|MockObject $urlGenerator */
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->method("generate")
                     ->willReturn("/url");

        $this->userResponder = new UserResponder($urlGenerator, $this->serializer);

        $this->hateoasManager = $this->createMock(HateoasManager::class);

        $this->userController = new UserController(
            $this->serializer,
            $this->userValidatorHelper,
            $this->userDBManager,
            $this->clientDBManager,
            $this->userResponder,
            $this->hateoasManager
        );
    }

    /********** ListAction **********/
    public function testListActionIfParametersRequestInvalidResponseErrors()
    {
        $request = new Request([
            "order" => "bla"
        ]);

        $constraintViolationList = new ConstraintViolationList([
            new ConstraintViolation(
                "msg",
                "msg",
                ["bla"],
                null,
                "order",
                "bla"
            )
        ]);

        $this->validatorInterface->method("validate")
                                 ->willReturn($constraintViolationList);

        $response = $this->userController->listAction($request);

        self::assertEquals("order", json_decode($response->getContent())->errors[0]->onProperty);
        self::assertEquals("msg", json_decode($response->getContent())->errors[0]->errorMessage);
        self::assertEquals("bla", json_decode($response->getContent())->errors[0]->valueGiven);
    }

    public function testListActionAllOkResponseDatas()
    {
        $session = (new Session(new MockArraySessionStorage()));
        $session->set('JWT', 'jwt');
        $request = new Request();
        $request->setSession($session);

        $constraintViolationList = new ConstraintViolationList([]);

        $this->validatorInterface->method("validate")
                                 ->willReturn($constraintViolationList);

        $datas = [
            "datas" => [
                $this->johnDoe,
                $this->janeDoe
            ],
            "metas" => [
                'nbTotalOfItems' => 2,
                'currentPage' => 1,
                'totalPages' => 1
            ]
        ];

        $this->userRepository->method("listWithPagination")
                             ->willReturn($datas);

        $this->hateoasManager->method("buildHateoas")->willReturn($datas);

        $response = $this->userController->listAction($request);

        self::assertEquals("John", json_decode($response->getContent())->datas[0]->firstname);
    }
}
