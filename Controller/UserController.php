<?php

namespace Symfonian\Indonesia\AdminBundle\Controller;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfonian\Indonesia\AdminBundle\Annotation\Schema\Grid;
use Symfonian\Indonesia\AdminBundle\Annotation\Schema\Page;
use Symfonian\Indonesia\AdminBundle\Annotation\Schema\Util\FileChooser;
use Symfonian\Indonesia\AdminBundle\Annotation\Schema\Util\Upload;

/**
 * @Route("/user")
 *
 * @Page(title="page.user.title", description="page.user.description")
 * @Grid(filter={"username", "full_name"})
 * @FileChooser()
 * @Upload("avatar")
 */
class UserController extends CrudController
{
}
