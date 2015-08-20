<?php

namespace Symfonian\Indonesia\AdminBundle\Twig\Number;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin.
 */

use DateTime;
use Twig_Extension;
use Twig_SimpleFunction;
use Symfonian\Indonesia\CoreBundle\Toolkit\Util\DateUtil\ValidIntervalChecker;

class ValidateIntervalFunction extends Twig_Extension
{
    public function getTests()
    {
        return array(
            new Twig_SimpleFunction('valid_interval', array($this, 'isValidInterval')),
        );
    }

    public function isValidInterval(DateTime $startDate, DateTime $endDate)
    {
        return ValidIntervalChecker::isValid($startDate, $endDate);
    }

    public function getName()
    {
        return 'valid_interval';
    }
}