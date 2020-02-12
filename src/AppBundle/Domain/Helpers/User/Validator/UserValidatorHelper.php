<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Domain\Helpers\User\Validator;

use AppBundle\Domain\DTO\Users\CreateUserDTO;
use AppBundle\Domain\DTO\Users\ListUserDTO;
use AppBundle\Domain\Helpers\Common\ExceptionManager;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserValidatorHelper
{
    /** @var ValidatorInterface */
    private $validator;
    /** @var ExceptionManager */
    private $exceptionManager;
    /** @var SerializerInterface */
    private $serializer;

    /**
     * ValidatorHelper constructor.
     * @param ValidatorInterface $validator
     * @param ExceptionManager $exceptionManager
     * @param SerializerInterface $serializer
     */
    public function __construct(
        ValidatorInterface $validator,
        ExceptionManager $exceptionManager,
        SerializerInterface $serializer
    ) {
        $this->validator = $validator;
        $this->exceptionManager = $exceptionManager;
        $this->serializer = $serializer;
    }

    /**
     * @param ParameterBag $params
     * @return ListUserDTO
     */
    public function listUserParameterValidate(ParameterBag $params)
    {
        // hydrate dto with default values
        $dto = new ListUserDTO(
            $params->get('name'),
            $params->get('firstname'),
            is_null($params->get('order')) ? "asc" : $params->get('order'),
            is_null($params->get('limit')) ? "10" : $params->get('limit'),
            is_null($params->get('offset')) ? "0" : $params->get('offset')
        );
        $this->checkErrors($dto);

        return $dto;
    }

    /**
     * @param string $content
     * @return CreateUserDTO
     */
    public function createUserParameterValidate(string $content)
    {
        /** @var CreateUserDTO $dto */
        $dto = $this->serializer->deserialize(
            $content,
            CreateUserDTO::class,
            'json'
        );
        $this->checkErrors($dto);

        return $dto;
    }

    /**
     * @param $dto
     */
    private function checkErrors($dto)
    {
        // check if dto is valid
        $errors = $this->validator->validate($dto);
        // if error(s), throw ValidatorException
        if (count($errors)) {
            $this->exceptionManager->validatorExceptionToJson($errors);
        }
    }
}
