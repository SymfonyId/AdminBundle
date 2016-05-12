<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\Doctrine\Orm\Filter;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;
use Symfonian\Indonesia\AdminBundle\Contract\FieldsFilterInterface;
use Symfonian\Indonesia\AdminBundle\Contract\SoftDeletableInterface;
use Symfonian\Indonesia\AdminBundle\Grid\Filter;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class FieldsFilter extends SQLFilter implements FieldsFilterInterface
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * Gets the SQL query part to add to a query.
     *
     * @param ClassMetadata $targetEntity
     * @param string        $targetTableAlias
     *
     * @return string The constraint SQL if there is available, empty string otherwise.
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        if (!$this->reader) {
            return '';
        }

        if (!$targetEntity->getReflectionClass()->implementsInterface(SoftDeletableInterface::class)) {
            return '';
        }

        $fields = array();
        $properties = $targetEntity->getReflectionProperties();
        /** @var \ReflectionProperty $property */
        foreach ($properties as $property) {
            $annotations = $this->reader->getPropertyAnnotations($property);
            foreach ($annotations as $annotation) {
                if ($annotation instanceof Filter) {
                    $fields[] = $property->getName();
                }
            }
        }

        $filter = '';
        foreach ($fields as $field) {
            $filter .= sprintf('%s.%s LIKE %%%s% OR', $targetTableAlias, $field, $this->getParameter('filter'));
        }

        return rtrim($filter, ' OR');
    }

    /**
     * @param Reader $reader
     */
    public function setAnnotationReader(Reader $reader)
    {
        $this->reader = $reader;
    }
}
