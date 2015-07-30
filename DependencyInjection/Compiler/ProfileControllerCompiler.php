<?php

namespace Symfonian\Indonesia\AdminBundle\DependencyInjection\Compiler;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfonian\Indonesia\AdminBundle\Controller\ProfileController;

final class ProfileControllerCompiler
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

        if (!$controller instanceof ProfileController) {
            return;
        }

        $controller->setShowFields($this->container->getParameter('symfonian_id.admin.profile_fields'));
        $controller->setFormClass($this->container->getParameter('symfonian_id.admin.security.change_password'));
    }
}
