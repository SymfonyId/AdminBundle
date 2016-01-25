<?php

namespace Symfonian\Indonesia\AdminBundle\View;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfonian\Indonesia\AdminBundle\Configuration\Configurator;
use Symfonian\Indonesia\AdminBundle\Controller\ProfileController;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

final class ProfileViewManipulator
{
    /**
     * @var Configurator
     */
    private $configuration;

    /**
     * @var string
     */
    private $profileFields;

    /**
     * @var string
     */
    private $formClass;

    public function __construct(Configurator $configuration)
    {
        $this->configuration = $configuration;
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

        $this->configuration->setFormClass($this->formClass);
        $this->configuration->setShowFields($this->profileFields);
    }
}
