<?php

namespace Symfonian\Indonesia\AdminBundle\Compiler;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

final class TwigGlobalVariableRegister
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $twig = $this->container->get('twig');
        $twig->addGlobal('title', $this->container->getParameter('symfonian_id.admin.app_title'));
        $twig->addGlobal('short_title', $this->container->getParameter('symfonian_id.admin.app_short_title'));
        $twig->addGlobal('date_time_format', $this->container->getParameter('symfonian_id.admin.date_time_format'));
        $twig->addGlobal('menu', $this->container->getParameter('symfonian_id.admin.menu'));
    }
}