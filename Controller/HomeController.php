<?php

namespace Symfonian\Indonesia\AdminBundle\Controller;

/*
 *
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 *
 */

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as Base;

class HomeController extends Base
{
    /**
     * @Route("/", name="home", options={"expose"=true})
     * @Method({"GET"})
     */
    public function indexAction()
    {
        return $this->render($this->container->getParameter('symfonian_id.admin.themes.dashboard'), array(
            'menu' => $this->container->getParameter('symfonian_id.admin.menu'),
        ));
    }
}
