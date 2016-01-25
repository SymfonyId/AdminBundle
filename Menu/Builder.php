<?php

namespace Symfonian\Indonesia\AdminBundle\Menu;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Translation\TranslatorInterface;

class Builder
{
    /**
     * @var \Symfony\Component\Routing\RouteCollection
     */
    protected $routeCollection;

    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    protected $translator;

    /**
     * @var string
     */
    protected $translationDomain;

    /**
     * @var \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @param Router $router
     * @param TranslatorInterface $translator
     * @param AuthorizationChecker $authorizationChecker
     * @param string $translationDomain
     */
    public function __construct(Router $router, TranslatorInterface $translator, AuthorizationChecker $authorizationChecker, $translationDomain)
    {
        $this->routeCollection = $router->getRouteCollection();
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param FactoryInterface $factory
     * @param array            $options
     *
     * @return ItemInterface
     */
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root', array(
            'childrenAttributes' => array(
                'class' => 'sidebar-menu',
            ),
        ));

        $menu->addChild('Home', array(
            'route' => 'home',
            'label' => sprintf('<i class="fa fa-dashboard"></i> <span>%s</span></a>', $this->translator->trans('menu.dashboard', array(), $this->translationDomain)),
            'extras' => array('safe_label' => true),
            'attributes' => array(
                'class' => 'treeview',
            ),
        ));

        $menu->addChild('Profile', array(
            'uri' => '#',
            'label' => sprintf('<i class="fa fa-user"></i> <span>%s</span><i class="fa fa-angle-double-left pull-right"></i></a>', $this->translator->trans('menu.profile', array(), $this->translationDomain)),
            'extras' => array('safe_label' => true),
            'attributes' => array(
                'class' => 'treeview',
            ),
        ));
        $menu['Profile']->setChildrenAttribute('class', 'treeview-menu');

        $menu['Profile']->addChild('UserProfile', array(
            'label' => $this->translator->trans('menu.profile', array(), $this->translationDomain),
            'route' => 'symfonian_indonesia_admin_profile_profile',
            'attributes' => array(
                'class' => 'treeview',
            ),
        ));

        $menu['Profile']->addChild('ChangePassword', array(
            'label' => $this->translator->trans('menu.user.change_password', array(), $this->translationDomain),
            'route' => 'symfonian_indonesia_admin_profile_changepassword',
            'attributes' => array(
                'class' => 'treeview',
            ),
        ));

        if ($this->routeCollection->get('symfonian_indonesia_admin_user_new') && $this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            $this->addUserMenu($menu);
        }

        return $menu;
    }

    protected function addUserMenu(ItemInterface $menu)
    {
        $menu->addChild('User', array(
            'uri' => '#',
            'label' => sprintf('<i class="fa fa-shield"></i> <span>%s</span><i class="fa fa-angle-double-left pull-right"></i></a>', $this->translator->trans('menu.user.title', array(), $this->translationDomain)),
            'extras' => array('safe_label' => true),
            'attributes' => array(
                'class' => 'treeview',
            ),
        ));

        $menu['User']->setChildrenAttribute('class', 'treeview-menu');

        $menu['User']->addChild('Add', array(
            'label' => $this->translator->trans('menu.user.add', array(), $this->translationDomain),
            'route' => 'symfonian_indonesia_admin_security_user_new',
            'attributes' => array(
                'class' => 'treeview',
            ),
        ));

        $menu['User']->addChild('List', array(
            'label' => $this->translator->trans('menu.user.list', array(), $this->translationDomain),
            'route' => 'symfonian_indonesia_admin_security_user_list',
            'attributes' => array(
                'class' => 'treeview',
            ),
        ));
    }
}
