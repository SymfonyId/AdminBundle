<?php

namespace Symfonian\Indonesia\AdminBundle\Template;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfonian\Indonesia\AdminBundle\Configuration\Configurator;
use Symfonian\Indonesia\AdminBundle\Controller\CrudController;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

final class CrudTemplateRegistrator
{
    /**
     * @var Configurator
     */
    private $configuration;

    /**
     * @var array
     */
    private $crudTemplate = array();

    /**
     * @var array
     */
    private $ajax = array();

    public function __construct(Configurator $configuration)
    {
        $this->configuration = $configuration;
    }

    public function setCrudTemplate(array $crudTemplate)
    {
        $this->crudTemplate = $crudTemplate;
    }

    public function setAjax(array $ajax)
    {
        $this->ajax = $ajax;
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

        $this->configuration->setNewTemplate($this->crudTemplate['new']);
        $this->configuration->setEditTemplate($this->crudTemplate['edit']);
        $this->configuration->setShowTemplate($this->crudTemplate['show']);
        $this->configuration->setListTemplate($this->crudTemplate['list']);
        $this->configuration->setAjaxTemplate(
            $this->ajax['template'],
            $this->ajax['use_ajax']
        );
    }
}
