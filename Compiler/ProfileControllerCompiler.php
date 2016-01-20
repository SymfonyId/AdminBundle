<?php

namespace Symfonian\Indonesia\AdminBundle\Compiler;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfonian\Indonesia\AdminBundle\Controller\ProfileController;
use Symfonian\Indonesia\AdminBundle\Handler\ConfigurationHandler;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

final class ProfileControllerCompiler
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
        if (!$controller instanceof ProfileController) {
            return;
        }

        $this->configuration->setShowFields($this->container->getParameter('symfonian_id.admin.profile_fields'));
        $this->configuration->setFormClass($this->container->getParameter('symfonian_id.admin.security.change_password'));
    }
}
