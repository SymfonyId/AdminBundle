<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class TwigVariableListener
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var array
     */
    private $variables = array();

    /**
     * @param \Twig_Environment $twig
     */
    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->variables['title'] = $title;
    }

    /**
     * @param string $shortTitle
     */
    public function setShortTitle($shortTitle)
    {
        $this->variables['short_title'] = $shortTitle;
    }

    /**
     * @param string $format
     */
    public function setDateTimeFormat($format)
    {
        $this->variables['date_format'] = $format;
    }

    /**
     * @param string $menu
     */
    public function setMenu($menu)
    {
        $this->variables['menu'] = $menu;
    }

    /**
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->variables['locale'] = $locale;
    }

    /**
     * @param string $translationDomain
     */
    public function setTranslationDomain($translationDomain)
    {
        $this->variables['translation_domain'] = $translationDomain;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $this->twig->addGlobal('title', $this->variables['title']);
        $this->twig->addGlobal('short_title', $this->variables['short_title']);
        $this->twig->addGlobal('date_time_format', $this->variables['date_format']);
        $this->twig->addGlobal('menu', $this->variables['menu']);
        $this->twig->addGlobal('locale', $this->variables['locale']);
        $this->twig->addGlobal('translation_domain', $this->variables['translation_domain']);
    }
}
