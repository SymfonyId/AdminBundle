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
class IncludeJavascript implements UtilAnnotationInterface
{
    private $file;

    private $includeRoute;

    public function setFile($file)
    {
        $this->file = $file;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setIncludeRoute($route)
    {
        if (! is_array($route)) {
            $route = (array) $route;
        }
        $this->includeRoute = $route;
    }

    public function getIncludeRoute()
    {
        return $this->includeRoute;
    }
}
