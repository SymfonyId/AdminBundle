<?php

namespace Symfonian\Indonesia\AdminBundle\Configuration;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin.
 */

use Doctrine\Common\Annotations\Reader;
use ReflectionClass;
use Symfonian\Indonesia\AdminBundle\Annotation\Grid;
use Symfonian\Indonesia\AdminBundle\Grid\Column;
use Symfonian\Indonesia\AdminBundle\Grid\Filter;

class GridConfigurator
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var ConfigurationFactory
     */
    private $configurationFactory;

    /**
     * @var array
     */
    private $columns = array();

    /**
     * @var array
     */
    private $filters = array();

    public function __construct(Reader $reader, ConfigurationFactory $configurationFactory)
    {
        $this->reader = $reader;
        $this->configurationFactory = $configurationFactory;
    }

    public function map($entity)
    {
        $reflection = new ReflectionClass($entity);
        foreach ($reflection->getProperties() as $reflectionProperty) {
            $annotations = $this->reader->getPropertyAnnotations($reflectionProperty);
            foreach ($annotations as $annotation) {
                if ($annotation instanceof Filter) {
                    $this->filters[] = $reflectionProperty->getName();
                }
                if ($annotation instanceof Column) {
                    $this->columns[] = $reflectionProperty->getName();
                }
            }
        }

        $grid = new Grid();
        $grid->setFilters($this->filters);
        $grid->setColumns($this->columns);

        $this->configurationFactory->addConfiguration($grid);
    }
}
