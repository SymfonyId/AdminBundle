<?php

namespace Symfonian\Indonesia\AdminBundle\View;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfonian\Indonesia\AdminBundle\Handler\ConfigurationHandler;
use Symfonian\Indonesia\AdminBundle\Controller\UserController;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

final class UserViewManipulator
{
    /**
     * @var ConfigurationHandler
     */
    private $configuration;

    private $formClass;

    private $entityClass;

    private $showFields;

    private $gridFields;

    public function __construct(ConfigurationHandler $configuration)
    {
        $this->configuration = $configuration;
    }

    public function setForm($formClass, $entityClass)
    {
        $this->formClass = $formClass;
        $this->entityClass = $entityClass;
    }

    public function setView(array $showFields, array $gridFields)
    {
        $this->showFields = $showFields;
        $this->gridFields = $gridFields;
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

        $this->configuration->setFormClass($this->formClass);
        $this->configuration->setEntityClass($this->entityClass);
        $this->configuration->setShowFields($this->showFields);
        $this->configuration->setGridFields($this->gridFields);
    }
}
