<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\View;

use Symfonian\Indonesia\AdminBundle\Annotation\Crud;
use Symfonian\Indonesia\AdminBundle\Configuration\Configurator;
use Symfonian\Indonesia\AdminBundle\Controller\ProfileController;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class ProfileViewManipulator
{
    /**
     * @var Configurator
     */
    private $configuration;

    private $profileFields;
    private $formClass;

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
        $crud = $this->configuration->getConfiguration(Crud::class);
        $crud->setFormClass($this->formClass);
        $crud->setShowFields($this->profileFields);

        $this->configuration->addConfiguration($crud);
    }
}
