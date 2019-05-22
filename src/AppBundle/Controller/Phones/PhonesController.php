<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Controller\Phones;

use Symfony\Component\Routing\Annotation\Route;

class PhonesController
{
    /**
     * @Route("/api/phones", name="list_phones", methods={"GET"})
     */
    public function listAction()
    {

    }
}
