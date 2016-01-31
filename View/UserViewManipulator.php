<?php

namespace Symfonian\Indonesia\AdminBundle\View;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfonian\Indonesia\AdminBundle\Annotation\Crud;
use Symfonian\Indonesia\AdminBundle\Annotation\Grid;
use Symfonian\Indonesia\AdminBundle\Configuration\ConfigurationFactory;
use Symfonian\Indonesia\AdminBundle\Controller\UserController;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class UserViewManipulator
{
    /**
     * @var ConfigurationFactory
     */
    protected $configurationFactory;

    protected $formClass;

    protected $entityClass;

    protected $showFields;

    protected $gridFields;

    protected $gridFilters;

    public function __construct(ConfigurationFactory $configurationFactory)
    {
        $this->configurationFactory = $configurationFactory;
    }

    public function setForm($formClass, $entityClass)
    {
        $this->formClass = $formClass;
        $this->entityClass = $entityClass;
    }

    public function setView(array $showFields, array $gridFields, array $gridFilters)
    {
        $this->showFields = $showFields;
        $this->gridFields = $gridFields;
        $this->gridFilters = $gridFilters;
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

        $crud = new Crud();
        $crud->setFormClass($this->formClass);
        $crud->setEntityClass($this->entityClass);
        $crud->setShowFields($this->showFields);

        $grid = new Grid();
        $grid->setColumns($this->gridFields);
        $grid->setFilters($this->gridFilters);

        $this->configurationFactory->addConfiguration($crud);
        $this->configurationFactory->addConfiguration($grid);
    }
}
