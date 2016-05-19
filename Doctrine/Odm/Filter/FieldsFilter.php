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

use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Query\Filter\BsonFilter;
use Symfonian\Indonesia\AdminBundle\Annotation\Grid;
use Symfonian\Indonesia\AdminBundle\Configuration\Configurator;
use Symfonian\Indonesia\AdminBundle\Contract\FieldsFilterInterface;
use Symfonian\Indonesia\AdminBundle\Extractor\ExtractorFactory;
use Symfonian\Indonesia\AdminBundle\Grid\Filter;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class FieldsFilter extends BsonFilter implements FieldsFilterInterface
{
    /**
     * @var ExtractorFactory
     */
    private $extractor;

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
            $this->extractor->extract($property);
            foreach ($this->extractor->getPropertyAnnotations() as $annotation) {
                if ($annotation instanceof Filter) {
                    $fields[] = $property->getName();
                }
            }
        }

        /** @var Grid $grid */
        $grid = $this->configurator->getConfiguration(Grid::class);
        $fields = !empty($fields) ? $fields : $grid->getFilters();

        foreach ($fields as $key => $field) {
            $fields[$key] = $targetDocument->getFieldMapping($field);
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
     * @param ExtractorFactory $extractor
     */
    public function setExtractor(ExtractorFactory $extractor)
    {
        $this->extractor = $extractor;
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
