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
use Symfonian\Indonesia\AdminBundle\Annotation\FilterFields;

/**
 * @Route("/user")
 *
 * @PageTitle("page.user.title")
 * @PageDescription("page.user.description")
 * @FilterFields({"username", "fullName"})
 */
class UserController extends CrudController
{
}
