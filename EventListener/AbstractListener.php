<?php

namespace Symfonian\Indonesia\AdminBundle\EventListener;

use Symfonian\Indonesia\AdminBundle\Controller\CrudController;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

abstract class AbstractListener
{
    private $controller;

    public function isValidCrudListener(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        if (!is_array($controller)) {
            return false;
        }

        $controller = $controller[0];
        if (!$controller instanceof CrudController) {
            return false;
        }

        $this->controller = $controller;

        return true;
    }

    public function getController()
    {
        return $this->controller;
    }
}