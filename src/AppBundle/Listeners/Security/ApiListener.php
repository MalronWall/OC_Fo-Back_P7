<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Listeners\Security;

use AppBundle\Domain\Helpers\Security\JWTManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class ApiListener
{
    /**
     * @var JWTManager
     */
    private $JWTManager;
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * ApiListener constructor.
     * @param JWTManager $JWTManager
     * @param ContainerInterface $container
     */
    public function __construct(
        JWTManager $JWTManager,
        ContainerInterface $container
    ) {
        $this->JWTManager = $JWTManager;
        $this->container = $container;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $jwt = $event->getRequest()->headers->get("Authorization");

        if (is_null($jwt)) {
            $event->setResponse(
                new Response(
                    json_encode(
                        ["errors" => "Bearer Authorization Token required"]
                    ),
                    Response::HTTP_UNAUTHORIZED,
                    [
                        'Content-Type' => 'application/json'
                    ]
                )
            );
            return;
        }

        $jwt = $this->JWTManager->buildWithString($jwt);

        if (!$jwt->isValid($this->container->getParameter("jwt_secret"))) {
            $event->setResponse(
                new Response(
                    json_encode(
                        ["errors" => "Invalid JWT"]
                    ),
                    Response::HTTP_UNAUTHORIZED,
                    [
                        'Content-Type' => 'application/json'
                    ]
                )
            );
            return;
        }

        if (!$jwt->isOnDate()) {
            $event->setResponse(
                new Response(
                    json_encode(
                        ["errors" => "JWT expired"]
                    ),
                    Response::HTTP_UNAUTHORIZED,
                    [
                        'Content-Type' => 'application/json'
                    ]
                )
            );
            return;
        }

        if (!$this->JWTManager->isValidClient($jwt->getPayload())) {
            $event->setResponse(
                new Response(
                    json_encode(
                        ["errors" => "Invalid User JWT"]
                    ),
                    Response::HTTP_UNAUTHORIZED,
                    [
                        'Content-Type' => 'application/json'
                    ]
                )
            );
            return;
        }

        return;
    }
}
