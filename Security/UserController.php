<?php
namespace Symfonian\Indonesia\AdminBundle\Security;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: http://blog.khodam.org
 */

use Symfonian\Indonesia\AdminBundle\Controller\CrudController;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfonian\Indonesia\AdminBundle\Annotation\PageTitle;
use Symfonian\Indonesia\AdminBundle\Annotation\PageDescription;
use Symfonian\Indonesia\AdminBundle\Annotation\IncludeJavascript;

/**
 * @Route("/user")
 *
 * @PageTitle("page.user.title")
 * @PageDescription("page.user.description")
 */
class UserController extends CrudController
{
}
