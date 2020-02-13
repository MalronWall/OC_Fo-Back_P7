<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace Tests\AppBundle\Listeners\Security;

use AppBundle\Domain\Entity\Client;
use AppBundle\Domain\Helpers\Security\JWTManager;
use AppBundle\Domain\Repository\ClientRepository;
use AppBundle\Listeners\Security\ApiListener;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ApiListenerTest extends KernelTestCase
{
    /** @var ApiListener */
    private $apiListener;
    /** @var ClientRepository|MockObject */
    private $clientRepository;
    /** @var KernelInterface */
    private $kernelMock;
    /** @var UrlGeneratorInterface */
    private $urlGenerator;
    /** @var JWTManager */
    private $JWTManager;

    public function setUp()
    {
        $this->kernelMock = self::bootKernel();
        /** @var ContainerInterface|MockObject $container */
        $container = $this->createMock(ContainerInterface::class);
        $container->method('getParameter')
                  ->willReturn('mon_jwt_secret');

        $this->clientRepository = $this->createMock(ClientRepository::class);
        /** @var EntityManagerInterface|MockObject $entityManager */
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('getRepository')
                      ->willReturn($this->clientRepository);

        /** @var UrlGeneratorInterface|MockObject $urlGenerator */
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->urlGenerator->method('generate')
                     ->willReturn('/url');

        $this->JWTManager = new JWTManager($container, $entityManager);

        $this->apiListener = new ApiListener(
            new JWTManager($container, $entityManager),
            $container,
            $this->urlGenerator
        );
    }

    /**
 * @throws \Doctrine\ORM\NonUniqueResultException
 */
    public function testIfRequestUrlNoneNoSendResponse()
    {
        $requestUrlNone = Request::create('/');

        $responseEvent = new GetResponseEvent(
            $this->kernelMock,
            $requestUrlNone,
            HttpKernelInterface::MASTER_REQUEST
        );

        $this->apiListener->onKernelRequest($responseEvent);
        self::assertNull($responseEvent->getResponse());
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testIfRequestUrlLoginNoSendResponse()
    {
        $requestUrlLogin = Request::create('/login');

        $responseEvent = new GetResponseEvent(
            $this->kernelMock,
            $requestUrlLogin,
            HttpKernelInterface::MASTER_REQUEST
        );

        $this->apiListener->onKernelRequest($responseEvent);
        self::assertNull($responseEvent->getResponse());
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testIfRequestUrlSFNoSendResponse()
    {
        $requestUrlSF = Request::create('/_');

        $responseEvent = new GetResponseEvent(
            $this->kernelMock,
            $requestUrlSF,
            HttpKernelInterface::MASTER_REQUEST
        );

        $this->apiListener->onKernelRequest($responseEvent);
        self::assertNull($responseEvent->getResponse());
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testIfRequestUrlAPIDocNoSendResponse()
    {
        $requestUrlApiDoc = Request::create('/api/doc');

        $responseEvent = new GetResponseEvent(
            $this->kernelMock,
            $requestUrlApiDoc,
            HttpKernelInterface::MASTER_REQUEST
        );

        $this->apiListener->onKernelRequest($responseEvent);
        self::assertNull($responseEvent->getResponse());
    }

    public function testIfJwtIsNull()
    {
        $request = Request::create('/api/BLA');

        $responseEvent = new GetResponseEvent(
            $this->kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST
        );

        $this->apiListener->onKernelRequest($responseEvent);

        $response = $responseEvent->getResponse();

        self::assertInstanceOf(Response::class, $response);
        self::assertEquals(
            "Bearer Authorization Token required",
            json_decode($response->getContent())->errors->errorMessage
        );
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        self::assertEquals("application/json", $response->headers->get("Content-Type"));
    }

    public function testIfJwtIsNotValid()
    {
        $request = Request::create('/api/BLA');
        $request->headers->set("Authorization", "BLA");

        $responseEvent = new GetResponseEvent(
            $this->kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST
        );

        $this->apiListener->onKernelRequest($responseEvent);

        $response = $responseEvent->getResponse();

        self::assertInstanceOf(Response::class, $response);
        self::assertEquals(
            "Invalid JWT",
            json_decode($response->getContent())->errors->errorMessage
        );
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        self::assertEquals("application/json", $response->headers->get("Content-Type"));
    }

    public function testIfJwtIsNotOnDate()
    {
        $request = Request::create('/api/BLA');
        $request->headers->set("Authorization", "eyJhbGciOiJIUzI1NiIsInR5cCI6IkF1dGhlbnRpZmljYXRpb25fSldUIn0.eyJzdWIiOiJkYzE5ZjUyNi00MjFhLTExZWEtYjc3Zi0yZTcyOGNlODgxMjUiLCJuYW1lIjoic2ZyIiwiaWF0IjoxNTgxNDQ0MTIwLCJleHAiOjE1ODE1MzA1MjB9.X2LmysN6W862hNdSCtxGovo30teWjZWfRkaQzDdDL4Q");

        $responseEvent = new GetResponseEvent(
            $this->kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST
        );

        $this->apiListener->onKernelRequest($responseEvent);

        $response = $responseEvent->getResponse();

        self::assertInstanceOf(Response::class, $response);
        self::assertEquals(
            "JWT expired",
            json_decode($response->getContent())->errors->errorMessage
        );
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        self::assertEquals("application/json", $response->headers->get("Content-Type"));
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Exception
     */
    public function testIfJwtIsNotValidUUIDClient()
    {
        $request = Request::create('/api/BLA');

        /** @var UuidInterface|MockObject $invalidUUID */
        $invalidUUID = $this->createMock(UuidInterface::class);
        $invalidUUID->method("toString")
                    ->willReturn("BLA");
        /** @var Client|MockObject $invalidClient */
        $invalidClient = $this->createMock(Client::class);
        $invalidClient->method("getId")
                      ->willReturn($invalidUUID);
        $invalidClient->method("getUsername")
                      ->willReturn("BLA");

        $jwt = $this->JWTManager->build($invalidClient);

        $request->headers->set("Authorization", $jwt->getJWT());

        $responseEvent = new GetResponseEvent(
            $this->kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST
        );

        $this->clientRepository->method("findClientById")
                               ->willReturn(null);

        $this->apiListener->onKernelRequest($responseEvent);

        $response = $responseEvent->getResponse();
        self::assertInstanceOf(Response::class, $response);
        self::assertEquals(
            "Invalid User JWT",
            json_decode($response->getContent())->errors->errorMessage
        );
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        self::assertEquals("application/json", $response->headers->get("Content-Type"));
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Exception
     */
    public function testIfJwtIsNotValidUsernameClient()
    {
        $request = Request::create('/api/BLA');

        /** @var UuidInterface|MockObject $UUID */
        $UUID = $this->createMock(UuidInterface::class);
        $UUID->method("toString")
                    ->willReturn("dc19f526-421a-11ea-b77f-111111111111");
        /** @var Client|MockObject $invalidClient */
        $invalidClient = $this->createMock(Client::class);
        $invalidClient->method("getId")
                      ->willReturn($UUID);
        $invalidClient->method("getUsername")
                      ->willReturn("BLA");

        $jwt = $this->JWTManager->build($invalidClient);

        $request->headers->set("Authorization", $jwt->getJWT());

        $responseEvent = new GetResponseEvent(
            $this->kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST
        );

        $clientTab["datas"][] = new Client("BLA2", "password");
        $this->clientRepository->method("findClientById")
                               ->willReturn($clientTab);

        $this->apiListener->onKernelRequest($responseEvent);

        $response = $responseEvent->getResponse();

        self::assertInstanceOf(Response::class, $response);
        self::assertEquals(
            "Invalid User JWT",
            json_decode($response->getContent())->errors->errorMessage
        );
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        self::assertEquals("application/json", $response->headers->get("Content-Type"));
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Exception
     */
    public function testIfJwtIsValid()
    {
        $request = Request::create('/api/BLA');
        $request->setSession(new Session(new MockArraySessionStorage()));

        /** @var UuidInterface|MockObject $UUID */
        $UUID = $this->createMock(UuidInterface::class);
        $UUID->method("toString")
                    ->willReturn("dc19f526-421a-11ea-b77f-111111111111");
        /** @var Client|MockObject $client */
        $client = $this->createMock(Client::class);
        $client->method("getId")
                      ->willReturn($UUID);
        $client->method("getUsername")
                      ->willReturn("BLA");

        $jwt = $this->JWTManager->build($client);

        $request->headers->set("Authorization", $jwt->getJWT());

        $responseEvent = new GetResponseEvent(
            $this->kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST
        );

        $clientTab["datas"][] = new Client("BLA", "password");
        $this->clientRepository->method("findClientById")
                               ->willReturn($clientTab);

        $this->apiListener->onKernelRequest($responseEvent);

        $response = $responseEvent->getResponse();

        self::assertNull($response);
        self::assertEquals(
            $responseEvent->getRequest()->getSession()->get("JWT"),
            $UUID->toString()
        );
    }
}
