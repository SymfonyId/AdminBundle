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

    public function __construct(array $data)
    {
        if (isset($data['value'])) {
            $this->file = $data['value'];
        }

        if (isset($data['file'])) {
            $this->file = $data['file'];
        }

        if (isset($data['includeRoute'])) {
            if (!is_array($data['includeRoute'])) {
                $data['includeRoute'] = (array) $data['includeRoute'];
            }

            $this->includeRoute = $data['includeRoute'];
        }
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getIncludeRoute()
    {
        return $this->includeRoute;
    }
}
