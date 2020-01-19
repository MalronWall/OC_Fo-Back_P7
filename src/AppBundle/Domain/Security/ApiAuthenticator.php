<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Domain\Security;

use AppBundle\Domain\DTO\Security\ApiLoginDTO;
use AppBundle\Domain\Entity\Client;
use AppBundle\Domain\Helpers\Security\JWTManager;
use AppBundle\Responder\Security\ApiResponder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiAuthenticator extends AbstractFormLoginAuthenticator
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;
    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;
    /**
     * @var ApiResponder
     */
    private $apiResponder;
    /**
     * @var JWTManager
     */
    private $JWTManager;

    /**
     * ApiAuthenticator constructor.
     * @param UrlGeneratorInterface $urlGenerator
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @param EntityManagerInterface $entityManager
     * @param EncoderFactoryInterface $encoderFactory
     * @param ApiResponder $apiResponder
     * @param JWTManager $JWTManager
     */
    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager,
        EncoderFactoryInterface $encoderFactory,
        ApiResponder $apiResponder,
        JWTManager $JWTManager
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->entityManager = $entityManager;
        $this->encoderFactory = $encoderFactory;
        $this->apiResponder = $apiResponder;
        $this->JWTManager = $JWTManager;
    }

    /**
     * Return the URL to the login page.
     *
     * @return string
     */
    protected function getLoginUrl()
    {
        return $this->urlGenerator->generate('login');
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function supports(Request $request)
    {
        return $request->attributes->get('_route') === 'login'
            && $request->isMethod('POST');
    }

    /**
     * Get the authentication credentials from the request and return them
     * as any type (e.g. an associate array).
     *
     * Whatever value you return here will be passed to getUser() and checkCredentials()
     *
     * For example, for a form login, you might:
     *
     *      return [
     *          'username' => $request->request->get('_username'),
     *          'password' => $request->request->get('_password'),
     *      ];
     *
     * Or for an API token that's on a header, you might use:
     *
     *      return ['api_key' => $request->headers->get('X-API-TOKEN')];
     *
     * @param Request $request
     *
     * @return mixed Any non-null value
     *
     * @throws \UnexpectedValueException If null is returned
     */
    public function getCredentials(Request $request)
    {
        $dto = $this->serializer->deserialize($request->getContent(), ApiLoginDTO::class, 'json');
        // check if dto is valid
        $errors = $this->validator->validate($dto);
        return count($errors) ? $errors : $dto;
    }

    /**
     * Return a UserInterface object based on the credentials.
     *
     * The *credentials* are the return value from getCredentials()
     *
     * You may throw an AuthenticationException if you wish. If you return
     * null, then a UsernameNotFoundException is thrown for you.
     *
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     *
     * @throws AuthenticationException
     *
     * @return UserInterface|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if ($credentials instanceof ConstraintViolationList) {
            $message = [];
            foreach ($credentials->getIterator() as $violation) {
                $message[$violation->getPropertyPath()] = $violation->getMessage();
            }
            throw new AuthenticationException(
                $this->serializer->serialize(['errors' => $message], 'json')
            );
        }
        $user = $this->entityManager->getRepository(Client::class)
                                    ->findClientByUsername($credentials->username);
        if (is_null($user)) {
            throw new AuthenticationException(
                $this->serializer->serialize(['error' => "Username not found !"], 'json')
            );
        }
        return $user;
    }

    /**
     * Returns true if the credentials are valid.
     *
     * If any value other than true is returned, authentication will
     * fail. You may also throw an AuthenticationException if you wish
     * to cause authentication to fail.
     *
     * The *credentials* are the return value from getCredentials()
     *
     * @param mixed $credentials
     * @param UserInterface $user
     *
     * @return bool
     *
     * @throws AuthenticationException
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        $valid = $this->encoderFactory->getEncoder(Client::class)
                                      ->isPasswordValid($user->getPassword(), $credentials->password, '');
        if (!$valid) {
            throw new AuthenticationException(
                $this->serializer->serialize(['error' => "Invalid password !"], 'json')
            );
        }
        return $valid;
    }

    /**
     * @param Request $request
     * @param AuthenticationException $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return $this->apiResponder->response(null, $exception->getMessage());
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        /** @var Client $client */
        $client = $token->getUser();
        $jwt = $this->JWTManager->build($client);
        return $this->apiResponder->response($this->serializer->serialize(['access' => $jwt->getJWT()], 'json'));
    }
}
