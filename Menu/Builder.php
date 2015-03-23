<?php
namespace Symfonian\Indonesia\AdminBundle\Menu;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: http://blog.khodam.org
 */

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Router;

class Builder
{
    /**
     * @var Symfony\Component\Routing\RouteCollection
     */
    protected $routeCollection;

    /**
     * @var Symfony\Component\Translation\TranslatorInterface
     */
    protected $translator;

    /**
     * @var string
     */
    protected $translationDomain;

    /**
     * @var Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    public function __construct(Router $router, ContainerInterface $container)
    {
        $this->routeCollection = $router->getRouteCollection();
        $this->translator = $container->get('translator');
        $this->translationDomain = $container->getParameter('symfonian_id.admin.translation_domain');
        $this->authorizationChecker = $container->get('security.authorization_checker');
    }

    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root', array(
            'childrenAttributes' => array(
                'class' => 'sidebar-menu'
            )
        ));

        $menu->addChild('Home', array(
            'route' => 'home',
            'label' => sprintf('<i class="fa fa-dashboard"></i> %s</a>', $this->translator->trans('menu.dashboard', array(), $this->translationDomain)),
            'extras' => array('safe_label' => true),
            'attributes' => array(
                'class' => 'treeview'
            )
        ));

        $menu->addChild('Profile', array(
            'uri' => '#',
            'label' => sprintf('<i class="fa fa-user"></i> %s<i class="fa fa-angle-double-left pull-right"></i></a>', $this->translator->trans('menu.profile', array(), $this->translationDomain)),
            'extras' => array('safe_label' => true),
            'attributes' => array(
                'class' => 'treeview'
            )
        ));
        $menu['Profile']->setChildrenAttribute('class', 'treeview-menu');

        $menu['Profile']->addChild('UserProfile', array(
            'label' => $this->translator->trans('menu.profile', array(), $this->translationDomain),
            'route' => 'symfonian_indonesia_admin_index_profile',
            'attributes' => array(
                'class' => 'treeview'
            )
        ));

        $menu['Profile']->addChild('ChangePassword', array(
            'label' => $this->translator->trans('menu.user.change_password', array(), $this->translationDomain),
            'route' => 'symfonian_indonesia_admin_index_changepassword',
            'attributes' => array(
                'class' => 'treeview'
            )
        ));

        if ($this->routeCollection->get('symfonian_indonesia_admin_security_user_new')) {
            $this->addUserMenu($menu);
        }

        return $menu;
    }

    protected function addUserMenu($menu)
    {
        if (! $this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            return ;
        }

        $menu->addChild('User', array(
            'uri' => '#',
            'label' => sprintf('<i class="fa fa-shield"></i> %s<i class="fa fa-angle-double-left pull-right"></i></a>', $this->translator->trans('menu.user.title', array(), $this->translationDomain)),
            'extras' => array('safe_label' => true),
            'attributes' => array(
                'class' => 'treeview'
            )
        ));

        $menu['User']->setChildrenAttribute('class', 'treeview-menu');

        $menu['User']->addChild('Add', array(
            'label' => $this->translator->trans('menu.user.add', array(), $this->translationDomain),
            'route' => 'symfonian_indonesia_admin_security_user_new',
            'attributes' => array(
                'class' => 'treeview'
            )
        ));

        $menu['User']->addChild('List', array(
            'label' => $this->translator->trans('menu.user.list', array(), $this->translationDomain),
            'route' => 'symfonian_indonesia_admin_security_user_list',
            'attributes' => array(
                'class' => 'treeview'
            )
        ));
    }
}
