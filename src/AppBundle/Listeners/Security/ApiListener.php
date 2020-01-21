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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * ApiListener constructor.
     * @param JWTManager $JWTManager
     * @param ContainerInterface $container
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        JWTManager $JWTManager,
        ContainerInterface $container,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->JWTManager = $JWTManager;
        $this->container = $container;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (preg_match("/\/login/", $event->getRequest()
                                          ->getRequestUri())) {
            return;
        }

        $jwt = $event->getRequest()->headers->get("Authorization");

        $msg["errors"]["loginUrl"] = $this->urlGenerator->generate("login");

        if (is_null($jwt)) {
            $msg["errors"]["errorMessage"] = "Bearer Authorization Token required";
            $event->setResponse(
                new Response(
                    json_encode($msg),
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
            $msg["errors"]["errorMessage"] = "Invalid JWT";
            $event->setResponse(
                new Response(
                    json_encode($msg),
                    Response::HTTP_UNAUTHORIZED,
                    [
                        'Content-Type' => 'application/json'
                    ]
                )
            );
            return;
        }

        if (!$jwt->isOnDate()) {
            $msg["errors"]["errorMessage"] = "JWT expired";
            $event->setResponse(
                new Response(
                    json_encode($msg),
                    Response::HTTP_UNAUTHORIZED,
                    [
                        'Content-Type' => 'application/json'
                    ]
                )
            );
            return;
        }

        if (!$this->JWTManager->isValidClient($jwt->getPayload())) {
            $msg["errors"]["errorMessage"] = "Invalid User JWT";
            $event->setResponse(
                new Response(
                    json_encode($msg),
                    Response::HTTP_UNAUTHORIZED,
                    [
                        'Content-Type' => 'application/json'
                    ]
                )
            );
            return;
        }

        $event->getRequest()
              ->getSession()
              ->set('JWT', $jwt->getPayload()->sub);

        return;
    }
}
