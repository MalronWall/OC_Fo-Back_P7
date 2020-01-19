<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Domain\Helpers\Phone\Validator;

use AppBundle\Domain\DTO\Phones\CreatePhoneDTO;
use AppBundle\Domain\DTO\Phones\ListPhoneDTO;
use AppBundle\Domain\Helpers\Common\ExceptionManager;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PhoneValidatorHelper
{
    /** @var ValidatorInterface */
    private $validator;
    /** @var ExceptionManager */
    private $exceptionManager;

    /**
     * ValidatorHelper constructor.
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

    /**
     * @param ParameterBag $params
     * @return ListPhoneDTO
     */
    public function listPhoneParameterValidate(ParameterBag $params)
    {
        // hydrate dto with default values
        $dto = new ListPhoneDTO(
            $params->get('brand'),
            $params->get('model'),
            $params->get('os'),
            is_null($params->get('order')) ? "asc" : $params->get('order'),
            is_null($params->get('limit')) ? "10" : $params->get('limit'),
            is_null($params->get('offset')) ? "0" : $params->get('offset')
        );
        $this->checkErrors($dto);

        return $dto;
    }

    /**
     * @param array $params
     * @return CreatePhoneDTO
     */
    public function createPhoneParameterValidate(array $params)
    {
        // hydrate dto with default values
        $dto = new CreatePhoneDTO(
            $params['brand'],
            $params['model'],
            $params['os'],
            (float)$params['price'],
            $params['cpu'],
            $params['gpu'],
            $params['ram'],
            $params['memory'],
            $params['dimensions'],
            $params['weight'],
            $params['resolution'],
            $params['mainCamera'],
            $params['selfieCamera'],
            $params['sound'],
            $params['battery'],
            $params['colors']
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
