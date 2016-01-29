<?php

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

namespace Symfonian\Indonesia\AdminBundle\Generator;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;

class FieldMapping
{
    protected static $fieldMapping = array(
        'smallint' => NumberType::class,
        'integer' => NumberType::class,
        'bingint' => NumberType::class,
        'float' => NumberType::class,
        'decimal' => NumberType::class,
        'string' => TextType::class,
        'gui' => TextType::class,
        'binary' => TextType::class,
        'blob' => TextType::class,
        'text' => TextareaType::class,
        'boolean' => CheckboxType::class,
        'date' => DateType::class,
        'datetime' => DateType::class,
        'datetimetz' => DateType::class,
        'time' => TimeType::class,
        'dateinterval' => DateType::class,
        'object' => TextType::class,
    );

    public static function convertToDoctrineType($formType)
    {

    }

    public static function convertToFormType($doctrineType)
    {

    }
}