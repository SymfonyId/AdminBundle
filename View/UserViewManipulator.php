<?php

namespace Symfonian\Indonesia\AdminBundle\View;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfonian\Indonesia\AdminBundle\Annotation\Schema\Crud;
use Symfonian\Indonesia\AdminBundle\Annotation\Schema\Grid;
use Symfonian\Indonesia\AdminBundle\Configuration\ConfigurationFactory;
use Symfonian\Indonesia\AdminBundle\Controller\UserController;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

final class UserViewManipulator
{
    /**
     * @var ConfigurationFactory
     */
    private $configurationFactory;

    private $formClass;

    private $entityClass;

    private $showFields;

    private $gridFields;

    private $gridFilters;

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
        $grid->setGridFields($this->gridFields);
        $grid->setGridFilters($this->gridFilters);

        $this->configurationFactory->addConfiguration($crud);
        $this->configurationFactory->addConfiguration($grid);
    }
}
