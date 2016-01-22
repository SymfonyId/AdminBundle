<?php

namespace Symfonian\Indonesia\AdminBundle\Configuration;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

class Configurator
{
    private $configurations = array();

    public function setConfiguration(array $configurations)
    {
        $this->configurations = $configurations;
    }
}
