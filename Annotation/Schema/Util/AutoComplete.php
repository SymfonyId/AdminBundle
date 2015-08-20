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

    public function __construct(array $data)
    {
        if (isset($data['value'])) {
            $this->route = $data['value'];
        }

        if (isset($data['route'])) {
            $this->route = $data['route'];
        }

        if (isset($data['targetSelector'])) {
            $this->targetSelector = $data['targetSelector'];
        }
    }

    public function getRoute()
    {
        return $this->route;
    }

    public function getTargetSelector()
    {
        return $this->targetSelector;
    }
}
