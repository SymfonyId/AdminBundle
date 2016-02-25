<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfonian\Indonesia\AdminBundle\Annotation\Crud;
use Symfonian\Indonesia\AdminBundle\Controller\UserController;
use Symfonian\Indonesia\AdminBundle\Extractor\ClassExtractor;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class Builder
{
    /**
     * @var Router
     */
    private $router;

    /**
     * @var ClassExtractor
     */
    private $extractor;

    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    private $translator;

    /**
     * @var \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    private $translationDomain;

    /**
     * @param Router               $router
     * @param ClassExtractor       $extractor
     * @param TranslatorInterface  $translator
     * @param AuthorizationChecker $authorizationChecker
     * @param string               $translationDomain
     */
    public function __construct(Router $router, ClassExtractor $extractor, TranslatorInterface $translator, AuthorizationChecker $authorizationChecker, $translationDomain)
    {
        $this->router = $router;
        $this->extractor = $extractor;
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
        $menu = $this->createRootMenu($factory, $options);
        $this->addDashboardMenu($menu);
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $this->addUserMenu($menu);
        }

        $this->generateMenu($menu);

        return $menu;
    }

    protected function isGranted($role)
    {
        return $this->authorizationChecker->isGranted($role);
    }

    protected function createRootMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root', array(
            'childrenAttributes' => array(
                'class' => 'sidebar-menu',
            ),
        ));

        return $menu;
    }

    protected function addDashboardMenu(ItemInterface $menu)
    {
        $this->addMenu($menu, 'home', 'menu.dashboard');
        $this->addMenu($menu, 'symfonian_indonesia_admin_profile_profile', 'menu.profile');
        $this->addMenu($menu, 'symfonian_indonesia_admin_profile_changepassword', 'menu.user.change_password');
    }

    protected function addUserMenu(ItemInterface $menu)
    {
        $this->addMenu($menu, 'symfonian_indonesia_admin_user_list', 'menu.user.title');
    }

    protected function addMenu(ItemInterface $menu, $route, $name, $icon = 'fa-bars')
    {
        $menu->addChild($name, array(
            'route' => $route,
            'label' => sprintf('<i class="fa %s"></i> <span>%s</span>', $icon, $this->translator->trans($name, array(), $this->translationDomain)),
            'extras' => array('safe_label' => true),
            'attributes' => array(
                'class' => 'treeview',
            ),
        ));
    }

    protected function generateMenu(ItemInterface $menu)
    {
        $routeCollection = $this->router->getRouteCollection()->all();
        $matches = array();
        /** @var Route $route */
        foreach ($routeCollection as $name => $route) {
            if (preg_match('/\/list\//', $route->getPath())) {
                $matches[$name] = $route;
            }
        }

        /** @var Route $route */
        foreach ($matches as $name => $route) {
            if ($temp = $route->getDefault('_controller')) {
                $controller = explode('::', $temp);

                $reflectionController = new \ReflectionClass($controller[0]);
                $annotations = $this->extractor->extract($reflectionController);
                foreach ($annotations as $annotation) {
                    if ($annotation instanceof Crud && !$annotation instanceof UserController) {
                        $this->addMenu($menu, $name, str_replace('Controller', '', $reflectionController->getShortName()), $annotation->getMenuIcon());
                    }
                }
            }
        }
    }
}
