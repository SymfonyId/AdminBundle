<?php

namespace Symfonian\Indonesia\AdminBundle\View;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfonian\Indonesia\AdminBundle\Annotation\Schema\Crud;
use Symfonian\Indonesia\AdminBundle\Configuration\ConfigurationFactory;
use Symfonian\Indonesia\AdminBundle\Controller\ProfileController;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

final class ProfileViewManipulator
{
    /**
     * @var ConfigurationFactory
     */
    private $configurationFactory;

    /**
     * @var string
     */
    private $profileFields;

    /**
     * @var string
     */
    private $formClass;

    public function __construct(ConfigurationFactory $configurationFactory)
    {
        $this->configurationFactory = $configurationFactory;
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

        $crud = new Crud();
        $crud->setFormClass($this->formClass);
        $crud->setShowFields($this->profileFields);

        $this->configurationFactory->addConfiguration($crud);
    }
}
