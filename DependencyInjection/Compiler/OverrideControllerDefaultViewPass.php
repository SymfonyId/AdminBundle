<?php
namespace Symfonian\Indonesia\AdminBundle\DependencyInjection\Compiler;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfonian\Indonesia\AdminBundle\Controller\CrudController;

final class OverrideControllerDefaultViewPass
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

        if (! is_array($controller)) {

            return;
        }

        $controller = $controller[0];

        if (! $controller instanceof CrudController) {

            return;
        }

        $controller->setNewTemplate($this->container->getParameter('symfonian_id.admin.themes.new_view'));
        $controller->setEditTemplate($this->container->getParameter('symfonian_id.admin.themes.edit_view'));
        $controller->setShowTemplate($this->container->getParameter('symfonian_id.admin.themes.show_view'));
        $controller->setListTemplate($this->container->getParameter('symfonian_id.admin.themes.list_view'));
        $controller->setFilterFields($this->container->getParameter('symfonian_id.admin.filter'));
        $controller->setListAjaxTemplate(
            $this->container->getParameter('symfonian_id.admin.themes.list_ajax'),
            $this->container->getParameter('symfonian_id.admin.list.use_ajax')
        );
    }
}
