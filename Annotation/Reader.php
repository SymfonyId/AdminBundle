<?php

namespace Symfonian\Indonesia\AdminBundle\Annotation;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Doctrine\Common\Annotations\Reader as BaseReader;
use ReflectionObject;
use Symfonian\Indonesia\AdminBundle\Configuration\ConfigurationFactory;
use Symfonian\Indonesia\AdminBundle\Configuration\ConfigurationInterface;
use Symfonian\Indonesia\AdminBundle\Controller\CrudController;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class Reader
{
    /**
     * @var BaseReader
     */
    protected $reader;

    /**
     * @var ConfigurationFactory
     */
    protected $configurationFactory;

    public function __construct(BaseReader $reader, ConfigurationFactory $configurationFactory)
    {
        $this->reader = $reader;
        $this->configurationFactory = $configurationFactory;
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

        $reflectionObject = new ReflectionObject($controller);
        unset($controller);
        foreach ($this->reader->getClassAnnotations($reflectionObject) as $annotation) {
            if ($annotation instanceof ConfigurationInterface) {
                $this->configurationFactory->addConfiguration($annotation);
            }
        }
    }
}
