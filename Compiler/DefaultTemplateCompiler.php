<?php

namespace Symfonian\Indonesia\AdminBundle\Compiler;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfonian\Indonesia\AdminBundle\Controller\CrudController;
use Symfonian\Indonesia\AdminBundle\Handler\ConfigurationHandler;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

final class DefaultTemplateCompiler
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

        if (!$controller instanceof CrudController) {
            return;
        }

        $this->configuration->setNewTemplate($this->container->getParameter('symfonian_id.admin.themes.new_view'));
        $this->configuration->setEditTemplate($this->container->getParameter('symfonian_id.admin.themes.edit_view'));
        $this->configuration->setShowTemplate($this->container->getParameter('symfonian_id.admin.themes.show_view'));
        $this->configuration->setListTemplate($this->container->getParameter('symfonian_id.admin.themes.list_view'));
        $this->configuration->setFilter($this->container->getParameter('symfonian_id.admin.filter'));
        $this->configuration->setAjaxTemplate(
            $this->container->getParameter('symfonian_id.admin.themes.ajax_template'),
            $this->container->getParameter('symfonian_id.admin.list.use_ajax')
        );
    }
}
