<?php

namespace Symfonian\Indonesia\AdminBundle\Extractor;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin.
 */
interface ExtractorInterface
{
    public function extract(\Reflection $reflectionClass);
}
