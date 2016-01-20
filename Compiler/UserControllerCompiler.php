<?php

namespace Symfonian\Indonesia\AdminBundle\Compiler;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfonian\Indonesia\AdminBundle\Handler\ConfigurationHandler;
use Symfonian\Indonesia\AdminBundle\Security\Controller\UserController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

final class UserControllerCompiler
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var ConfigurationHandler
     */
    private $configuration;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->configuration = $container->get('symfonian_id.admin.handler.configuration');
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

        $this->configuration->setForm($this->container->getParameter('symfonian_id.admin.security.user_form'));
        $this->configuration->setEntity($this->container->getParameter('symfonian_id.admin.security.user_entity'));
        $this->configuration->setShowFields($this->container->getParameter('symfonian_id.admin.security.show_fields'));
        $this->configuration->setGridFields($this->container->getParameter('symfonian_id.admin.security.grid_fields'));
    }
}
