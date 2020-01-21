<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Domain\Helpers\Common;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\ValidatorException;

class ExceptionManager
{
    /**
     * @param ConstraintViolationListInterface $errors
     */
    public function validatorExceptionToJson(ConstraintViolationListInterface $errors)
    {
        $msg = [];
        foreach ($errors as $i => $error) {
            $msg["errors"][$i]["onProperty"] = $error->getPropertyPath();
            $msg["errors"][$i]["errorMessage"] = $error->getMessage();
            $msg["errors"][$i]["valueGiven"] = $error->getInvalidValue();
        }
        throw new ValidatorException(json_encode($msg));
    }

    public function pageNotFoundExceptionToJson($value = null, $field = "id")
    {
        $msg = [];
        $msg["errors"]["onProperty"] = $field;
        $msg["errors"]["errorMessage"] = "This field does not correspond to anything.";
        $msg["errors"]["valueGiven"] = $value;

        throw new NotFoundHttpException(json_encode($msg));
    }

    public function unauthorizedAccessExceptionToJson($value = null, $field = "id")
    {
        $msg = [];
        $msg["errors"]["onProperty"] = $field;
        $msg["errors"]["errorMessage"] = "You don't have the access to this user.";
        $msg["errors"]["valueGiven"] = $value;

        throw new UnauthorizedHttpException(null, json_encode($msg));
    }
}
