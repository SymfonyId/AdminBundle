<?php

namespace Symfonian\Indonesia\AdminBundle\Security\Controller;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfonian\Indonesia\AdminBundle\Controller\CrudController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfonian\Indonesia\AdminBundle\Annotation\Schema\Page;
use Symfonian\Indonesia\AdminBundle\Annotation\Schema\Grid;

/**
 * @Route("/user")
 *
 * @Page(title="page.user.title", description="page.user.description")
 * @Grid(filter={"username", "fullName"})
 */
class UserController extends CrudController
{
}
