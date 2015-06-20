<?php
namespace Symfonian\Indonesia\AdminBundle\Annotation;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class ShowFields
{
    public $value;

    public function isValid()
    {
        if (! is_array($this->value)) {
            return false;
        }

        return true;
    }
}
