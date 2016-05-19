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

use Symfonian\Indonesia\AdminBundle\Annotation\Crud;
use Symfonian\Indonesia\AdminBundle\Controller\CrudController;
use Symfonian\Indonesia\AdminBundle\Extractor\ExtractorFactory;
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
     * @var ExtractorFactory
     */
    private $extractor;

    /**
     * @var string
     */
    private $driver;

    public function __construct(ExtractorFactory $extractor, $driver)
    {
        $this->extractor = $extractor;
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
     * @param string|null $entityClass
     *
     * @return string
     */
    public function getDriver($entityClass = null)
    {
        $this->overrideDefaultDriver($entityClass);

        return $this->driver;
    }

    /**
     * @param string $driver
     */
    protected function setDriver($driver)
    {
        $this->driver = $driver;
    }

    /**
     * @return ExtractorFactory
     */
    protected function getExtractor()
    {
        return $this->extractor;
    }

    /**
     * @return CrudController
     */
    protected function getController()
    {
        return $this->controller;
    }

    private function overrideDefaultDriver($entityClass = null)
    {
        /*
         * Override default driver
         */
        if (!$entityClass) {
            $reflectionController = new \ReflectionObject($this->getController());
            $this->extractor->extract($reflectionController);
            foreach ($this->extractor->getClassAnnotations() as $annotation) {
                if ($annotation instanceof Crud) {
                    $entityClass = $annotation->getEntityClass();

                    break;
                }
            }
        }

        if ($entityClass) {
            $reflectionEntity = new \ReflectionClass($entityClass);
            $this->extractor->extract($reflectionEntity);
            foreach ($this->extractor->getClassAnnotations() as $annotation) {
                if ($annotation instanceof Driver) {
                    $this->driver = $annotation->getDriver();

                    break;
                }
            }
        }
    }
}
