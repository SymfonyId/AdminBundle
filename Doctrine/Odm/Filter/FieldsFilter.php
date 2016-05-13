<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\Doctrine\Odm\Filter;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Query\Filter\BsonFilter;
use Symfonian\Indonesia\AdminBundle\Configuration\Configurator;
use Symfonian\Indonesia\AdminBundle\Contract\FieldsFilterInterface;
use Symfonian\Indonesia\AdminBundle\Grid\Filter;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class FieldsFilter extends BsonFilter implements FieldsFilterInterface
{
    /**
     * @var Reader
     */
    private $reader;
    
    /**
     * @var Configurator
     */
    private $configurator;

    /**
     * @var string
     */
    private $dateTimeFormat;

    /**
     * Gets the criteria array to add to a query.
     *
     * If there is no criteria for the class, an empty array should be returned.
     *
     * @param ClassMetadata $targetDocument
     *
     * @return array
     */
    public function addFilterCriteria(ClassMetadata $targetDocument)
    {
        $fields = array();
        $properties = $targetDocument->getReflectionProperties();
        /** @var \ReflectionProperty $property */
        foreach ($properties as $property) {
            $annotations = $this->reader->getPropertyAnnotations($property);
            foreach ($annotations as $annotation) {
                if ($annotation instanceof Filter) {
                    $fields[] = $property->getName();
                }
            }
        }

        $output = array();
        foreach ($fields as $field) {
            if (in_array($field['type'], array('date', 'datetime', 'time'))) {
                $date = \DateTime::createFromFormat($this->dateTimeFormat, $this->getParameter('filter'));
                if ($date) {
                    $output[$field['fieldName']] = $date->format($this->dateTimeFormat);
                }
            } else {
                $output[$field['fieldName']] = new \MongoRegex(sprintf('/.*%s.*/i', $this->getParameter('filter')));
            }
        }

        return $output;
    }

    /**
     * @param Reader $reader
     */
    public function setAnnotationReader(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param string $format
     */
    public function setDateTimeFormat($format)
    {
        $this->dateTimeFormat = $format;
    }

    /**
     * @param Configurator $configurator
     */
    public function setConfigurator(Configurator $configurator)
    {
        $this->configurator = $configurator;
    }
}