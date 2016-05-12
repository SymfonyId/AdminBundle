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
use Doctrine\ORM\Query\Filter\SQLFilter;
use Symfonian\Indonesia\AdminBundle\Contract\FieldsFilterInterface;
use Symfonian\Indonesia\AdminBundle\Controller\CrudController;
use Symfonian\Indonesia\AdminBundle\Manager\Driver;
use Symfonian\Indonesia\AdminBundle\Manager\ManagerFactory;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class EnableFieldsFilterListener
{
    /**
     * @var ManagerFactory
     */
    private $managerFactory;

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var string
     */
    private $driver;

    /**
     * @var string
     */
    private $dateTimeFormat;

    /**
     * @param ManagerFactory $managerFactory
     * @param Reader         $reader
     * @param string         $driver
     * @param string         $dateTimeFormat
     */
    public function __construct(ManagerFactory $managerFactory, Reader $reader, $driver, $dateTimeFormat)
    {
        $this->managerFactory = $managerFactory;
        $this->reader = $reader;
        $this->driver = $driver;
        $this->dateTimeFormat = $dateTimeFormat;
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

        $request = $event->getRequest();
        if (!$request->query->get('filter')) {
            return;
        }

        $driver = $this->driver;

        /**
         * Override default driver
         */
        $reflectionController = new \ReflectionObject($controller);
        $properties = $reflectionController->getProperties(\ReflectionProperty::IS_PRIVATE|\ReflectionProperty::IS_PROTECTED);
        foreach ($properties as $property) {
            $annotations = $this->reader->getPropertyAnnotations($property);
            foreach ($annotations as $annotation) {
                if ($annotation instanceof Driver) {
                    $driver = $annotation->getDriver();
                }
            }
        }

        $manager = $this->managerFactory->getManager($driver);
        if (ManagerFactory::DOCTRINE_ORM === $driver) {
            $filter = $manager->getFilters()->enable('symfonian_id.admin.filter.orm.fields');
            $this->applyFilter($filter, $request->query->get('filter'));
        }

        if (ManagerFactory::DOCTRINE_ODM === $driver) {
            $filter = $manager->getFilters()->enable('symfonian_id.admin.filter.odm.fields');
            $this->applyFilter($filter, $request->query->get('filter'));
        }
    }

    /**
     * @param FieldsFilterInterface|SQLFilter $filter
     * @param string                          $keyword
     */
    private function applyFilter(FieldsFilterInterface $filter, $keyword)
    {
        $filter->setAnnotationReader($this->reader);
        $filter->setDateTimeFormat($this->dateTimeFormat);
        $filter->setParameter('keyword', $keyword);
    }
}
