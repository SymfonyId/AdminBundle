<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\EventListener;

use Doctrine\Common\Annotations\Reader;
use Symfonian\Indonesia\AdminBundle\Annotation\Crud;
use Symfonian\Indonesia\AdminBundle\Controller\CrudController;
use Symfonian\Indonesia\AdminBundle\Manager\Driver;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
abstract class AbstractListener
{
    /**
     * @var CrudController
     */
    private $controller;

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var string
     */
    private $driver;

    public function __construct(Reader $reader, $driver)
    {
        $this->reader = $reader;
        $this->driver = $driver;
    }

    /**
     * @param FilterControllerEvent $event
     *
     * @return bool
     */
    protected function isValidCrudListener(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        if (!is_array($controller)) {
            return false;
        }

        $controller = $controller[0];
        if (!$controller instanceof CrudController) {
            return false;
        }

        $this->controller = $controller;

        return true;
    }

    /**
     * @return string
     */
    protected function getDriver()
    {
        $this->overrideDefaultDriver();

        return $this->driver;
    }

    /**
     * @return Reader
     */
    protected function getReader()
    {
        return $this->reader;
    }

    /**
     * @return CrudController
     */
    protected function getController()
    {
        return $this->controller;
    }

    private function overrideDefaultDriver()
    {
        /*
         * Override default driver
         */
        $entityClass = null;
        $reflectionController = new \ReflectionObject($this->getController());
        $annotations = $this->reader->getClassAnnotations($reflectionController);
        foreach ($annotations as $annotation) {
            if ($annotation instanceof Crud) {
                $entityClass = $annotation->getEntityClass();

                break;
            }
        }

        if ($entityClass) {
            $reflectionEntity = new \ReflectionClass($entityClass);
            $annotations = $this->reader->getClassAnnotations($reflectionEntity);
            foreach ($annotations as $annotation) {
                if ($annotation instanceof Driver) {
                    $this->driver = $annotation->getDriver();

                    break;
                }
            }
        }
    }
}
