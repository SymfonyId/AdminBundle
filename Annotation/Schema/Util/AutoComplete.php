<?php

namespace Symfonian\Indonesia\AdminBundle\Annotation\Schema\Util;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin.
 */

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class AutoComplete implements UtilAnnotationInterface
{
    private $route;

    private $targetSelector;

    public function setValue($route)
    {
        $this->setRoute($route);
    }

    public function setRoute($route)
    {
        $this->route = $route;
    }

    public function getRoute()
    {
        return $this->route;
    }

    public function setTargetSelector($targetSelector)
    {
        $this->targetSelector = $targetSelector;
    }

    public function getTargetSelector()
    {
        return $this->targetSelector;
    }
}
