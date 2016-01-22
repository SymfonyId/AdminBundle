<?php

namespace Symfonian\Indonesia\AdminBundle\Twig;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

final class GlobalVariableRegistrator
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var array
     */
    private $variables;

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
    }
}
