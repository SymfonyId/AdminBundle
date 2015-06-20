<?php
namespace Symfonian\Indonesia\AdminBundle\Controller;

/*
 *
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 *
 */

use Symfony\Bundle\FrameworkBundle\Controller\Controller as SfController;

class HomeController extends SfController
{
    public function indexAction()
    {
        return $this->render($this->container->getParameter('symfonian_id.admin.themes.dashboard'), array(
            'menu' => $this->container->getParameter('symfonian_id.admin.menu'),
        ));
    }
}
