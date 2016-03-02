<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\Twig\Comparations;

use Symfonian\Indonesia\AdminBundle\SymfonianIndonesiaAdminConstants;
use Symfony\Component\HttpFoundation\Session\Session;
use Twig_Extension;
use Twig_SimpleTest;

class SortedTest extends Twig_Extension
{
    /**
     * @var Session
     */
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function getTests()
    {
        return array(
            new Twig_SimpleTest('sorted', array($this, 'isSorted')),
        );
    }

    public function isSorted($field)
    {
        $sessionField = $this->session->get(SymfonianIndonesiaAdminConstants::SESSION_SORTED_NAME);
        if ($field === $sessionField) {
            return true;
        }

        return false;
    }

    public function getName()
    {
        return 'is_sorted';
    }
}
