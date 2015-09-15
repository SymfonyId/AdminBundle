<?php

namespace Symfonian\Indonesia\AdminBundle\Compiler;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfonian\Indonesia\AdminBundle\Security\Controller\UserController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

final class UserControllerRegister
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        if (!is_array($controller)) {
            return;
        }

        $controller = $controller[0];

        if (!$controller instanceof UserController) {
            return;
        }

        $controller->setForm($this->container->getParameter('symfonian_id.admin.security.user_form'));
        $controller->setEntity($this->container->getParameter('symfonian_id.admin.security.user_entity'));
        $controller->setShowFields($this->container->getParameter('symfonian_id.admin.security.show_fields'));
        $controller->setGridFields($this->container->getParameter('symfonian_id.admin.security.grid_fields'));
    }
}
