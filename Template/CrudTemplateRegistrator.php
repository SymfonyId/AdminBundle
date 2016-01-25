<?php

namespace Symfonian\Indonesia\AdminBundle\Template;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfonian\Indonesia\AdminBundle\Annotation\Schema\Crud;
use Symfonian\Indonesia\AdminBundle\Configuration\ConfigurationFactory;
use Symfonian\Indonesia\AdminBundle\Controller\CrudController;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

final class CrudTemplateRegistrator
{
    /**
     * @var ConfigurationFactory
     */
    private $configurationFactory;

    /**
     * @var array
     */
    private $crudTemplate = array();

    /**
     * @var array
     */
    private $ajax = array();

    public function __construct(ConfigurationFactory $configurationFactory)
    {
        $this->configurationFactory = $configurationFactory;
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

        $crud = new Crud();
        $crud->setAddTemplate($this->crudTemplate['new']);
        $crud->setEditTemplate($this->crudTemplate['edit']);
        $crud->setShowTemplate($this->crudTemplate['show']);
        $crud->setListTemplate($this->crudTemplate['list']);
        $crud->setAjaxTemplate($this->ajax['template'], $this->ajax['use_ajax']);

        $this->configurationFactory->addConfiguration($crud);
    }
}
