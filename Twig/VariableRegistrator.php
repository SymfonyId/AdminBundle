<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\Twig;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class VariableRegistrator
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    private $variables = array();

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function setVariables(array $variables)
    {
        $this->variables = $variables;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $this->twig->addGlobal('title', $this->variables['title']);
        $this->twig->addGlobal('short_title', $this->variables['short_title']);
        $this->twig->addGlobal('date_time_format', $this->variables['date_format']);
        $this->twig->addGlobal('menu', $this->variables['menu']);
        $this->twig->addGlobal('locale', $this->variables['locale']);
    }
}
