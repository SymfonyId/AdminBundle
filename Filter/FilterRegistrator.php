<?php

namespace Symfonian\Indonesia\AdminBundle\Filter;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfonian\Indonesia\AdminBundle\Configuration\Configurator;
use Symfonian\Indonesia\AdminBundle\Controller\CrudController;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

final class FilterRegistrator
{
    /**
     * @var Configurator
     */
    private $configuration;

    /**
     * @var array
     */
    private $filter = array();

    public function __construct(Configurator $configuration)
    {
        $this->configuration = $configuration;
    }

    public function setFilter(array $filter)
    {
        $this->filter = $filter;
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

        $this->configuration->setFilter($this->filter);
    }
}
