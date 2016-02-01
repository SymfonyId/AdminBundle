<?php

namespace Symfonian\Indonesia\AdminBundle\View;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfonian\Indonesia\AdminBundle\Annotation\Crud;
use Symfonian\Indonesia\AdminBundle\Configuration\Configurator;
use Symfonian\Indonesia\AdminBundle\Controller\ProfileController;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class ProfileViewManipulator
{
    /**
     * @var Configurator
     */
    protected $configuration;

    /**
     * @var string
     */
    protected $profileFields;

    /**
     * @var string
     */
    protected $formClass;

    public function __construct(Configurator $configurator)
    {
        $this->configuration = $configurator;
    }

    public function setFormClass($formClass)
    {
        $this->formClass = $formClass;
    }

    public function setProfileFields($profileFields)
    {
        $this->profileFields = $profileFields;
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

        /** @var Crud $crud */
        $crud = $this->configuration->getConfigForClass(Crud::class);
        $crud->setFormClass($this->formClass);
        $crud->setShowFields($this->profileFields);

        $this->configuration->addConfiguration($crud);
    }
}
