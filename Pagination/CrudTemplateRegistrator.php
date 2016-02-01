<?php

namespace Symfonian\Indonesia\AdminBundle\Template;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfonian\Indonesia\AdminBundle\Annotation\Crud;
use Symfonian\Indonesia\AdminBundle\Configuration\ConfigurationFactory;
use Symfonian\Indonesia\AdminBundle\Controller\CrudController;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class CrudTemplateRegistrator
{
    /**
     * @var ConfigurationFactory
     */
    protected $configurationFactory;

    /**
     * @var array
     */
    protected $crudTemplate = array();

    public function __construct(ConfigurationFactory $configurationFactory)
    {
        $this->configurationFactory = $configurationFactory;
    }

    public function setCrudTemplate(array $crudTemplate)
    {
        $this->crudTemplate = $crudTemplate;
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
        $crud->setCreateTemplate($this->crudTemplate['new']);
        $crud->setEditTemplate($this->crudTemplate['edit']);
        $crud->setShowTemplate($this->crudTemplate['show']);
        $crud->setListTemplate($this->crudTemplate['list']);

        $this->configurationFactory->addConfiguration($crud);
    }
}
