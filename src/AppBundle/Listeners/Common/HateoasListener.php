<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace AppBundle\Listeners\Common;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class HateoasListener
{
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (preg_match("/\/login/", $event->getRequest()->getRequestUri())) {
            return;
        }
    }
}
