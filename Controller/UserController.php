<?php

namespace Symfonian\Indonesia\AdminBundle\Controller;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfonian\Indonesia\AdminBundle\Annotation\Page;
use Symfonian\Indonesia\AdminBundle\Annotation\Util;

/**
 * @Route("/user")
 *
 * @Page(title="page.user.title", description="page.user.description")
 * @Util(fileChooser=true, uploadable="avatar")
 */
class UserController extends CrudController
{
    protected function getClassName()
    {
        return __CLASS__;
    }
}
