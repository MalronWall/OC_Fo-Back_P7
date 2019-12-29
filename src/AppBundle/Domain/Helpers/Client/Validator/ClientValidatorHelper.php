<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Domain\Helpers\Client\Validator;

use AppBundle\Domain\DTO\Clients\CreateClientDTO;
use AppBundle\Domain\Helpers\Common\ExceptionManager;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ClientValidatorHelper
{
    /** @var ValidatorInterface */
    private $validator;
    /** @var ExceptionManager */
    private $exceptionManager;

    /**
     * ClientValidatorHelper constructor.
     * @param ValidatorInterface $validator
     * @param ExceptionManager $exceptionManager
     */
    public function __construct(
        ValidatorInterface $validator,
        ExceptionManager $exceptionManager
    ) {
        $this->validator = $validator;
        $this->exceptionManager = $exceptionManager;
    }

    public function createClientParameterValidate(array $params)
    {
        // hydrate dto with default values
        $dto = new CreateClientDTO(
            $params['username'],
            $params['password']
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
