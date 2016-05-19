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

    public function onKernelRequest()
    {
        $needToMerge = array(
            'title' => $this->variables['title'],
            'short_title' => $this->variables['short_title'],
            'date_time_format' => $this->variables['date_format'],
            'menu' => $this->variables['menu'],
            'locale' => $this->variables['locale'],
            'translation_domain' => $this->variables['translation_domain'],
        );

        $globals = $this->twig->getGlobals();
        foreach ($needToMerge as $key => $value) {
            if (!array_key_exists($key, $globals)) {
                $this->twig->addGlobal($key, $value);
            }
        }
    }
}
