<?php
namespace Symfonian\Indonesia\AdminBundle\Annotation;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: http://blog.khodam.org
 */

/**
 * @Annotation
 * @Target({"CLASS"})
 */
final class Crud
{
    public $entityClass;

    public $formClass;

    public $gridFields;

    public $normalizeFilter;

    public $hasEventListener;

    public $showFields;

    public $pageTitle;

    public $pageDescription;

    public $newActionTemplate;

    public $editActionTemplate;

    public $showActionTemplate;

    public $listActionTemplate;
}
