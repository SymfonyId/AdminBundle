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
use Symfonian\Indonesia\AdminBundle\Exception\RuntimeException;
use Symfonian\Indonesia\AdminBundle\Extractor\ClassExtractor;
use Symfonian\Indonesia\AdminBundle\SymfonianIndonesiaAdminConstants as Constants;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class Builder
{
    /**
     * @var Kernel
     */
    private $kernel;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var ClassExtractor
     */
    private $extractor;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var string
     */
    private $translationDomain;

    /**
     * @param Kernel               $kernel
     * @param Router               $router
     * @param ClassExtractor       $extractor
     * @param TranslatorInterface  $translator
     * @param AuthorizationChecker $authorizationChecker
     * @param string               $translationDomain
     */
    public function __construct(Kernel $kernel, Router $router, ClassExtractor $extractor, TranslatorInterface $translator, AuthorizationChecker $authorizationChecker, $translationDomain)
    {
        $this->kernel = $kernel;
        $this->router = $router;
        $this->extractor = $extractor;
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param FactoryInterface $factory
     *
     * @return ItemInterface
     */
    public function mainMenu(FactoryInterface $factory)
    {
        $menu = $this->createRootMenu($factory);
        $this->addDashboardMenu($menu);
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $this->addUserMenu($menu);
        }

        $this->generateMenu($menu);

        return $menu;
    }

    /**
     * @param string $role
     *
     * @return bool
     */
    protected function isGranted($role)
    {
        return $this->authorizationChecker->isGranted($role);
    }

    /**
     * @param FactoryInterface $factory
     *
     * @return ItemInterface
     */
    protected function createRootMenu(FactoryInterface $factory)
    {
        $menu = $factory->createItem('root', array(
            'childrenAttributes' => array(
                'class' => 'sidebar-menu',
            ),
        ));

        return $menu;
    }

    /**
     * @param ItemInterface $menu
     */
    protected function addDashboardMenu(ItemInterface $menu)
    {
        $this->addMenu($menu, 'home', 'menu.dashboard');
        $this->addMenu($menu, 'symfonian_indonesia_admin_profile_profile', 'menu.profile');
        $this->addMenu($menu, 'symfonian_indonesia_admin_profile_changepassword', 'menu.user.change_password');
    }

    /**
     * @param ItemInterface $menu
     */
    protected function addUserMenu(ItemInterface $menu)
    {
        $this->addMenu($menu, 'symfonian_indonesia_admin_user_list', 'menu.user.title');
    }

    /**
     * @param ItemInterface $menu
     * @param string        $route
     * @param string        $name
     * @param string        $icon
     */
    protected function addMenu(ItemInterface $menu, $route, $name, $icon = 'fa-bars')
    {
        $menu->addChild($name, array(
            'route' => $route,
            'label' => sprintf('<i class="fa %s" aria-hidden="true"></i> <span>%s</span>', $icon, $this->translator->trans($name, array(), $this->translationDomain)),
            'extras' => array('safe_label' => true),
            'attributes' => array(
                'class' => 'treeview',
            ),
        ));
    }

    /**
     * @param ItemInterface $menu
     */
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

        $cacheFile = sprintf('%s/%s/%s.php.cache', $this->kernel->getCacheDir(), Constants::CACHE_DIR, str_replace('\\', '_', __CLASS__));
        if (file_exists($cacheFile) && 'prod' === strtolower($this->kernel->getEnvironment())) {
            $this->iterateMenu($menu, require $cacheFile);
        }

        $items = array();
        /** @var Route $route */
        foreach ($matches as $name => $route) {
            if ($temp = $route->getDefault('_controller')) {
                $controller = explode('::', $temp);

                $reflectionController = new \ReflectionClass($controller[0]);
                $annotations = $this->extractor->extract($reflectionController);
                foreach ($annotations as $annotation) {
                    if ($annotation instanceof Crud && !$annotation instanceof UserController) {
                        $items[$name] = array(
                            'name' => $this->translator->trans(sprintf('menu.label.%s', strtolower(str_replace('Controller', '', $reflectionController->getShortName()))), array(), $this->translationDomain),
                            'icon' => $annotation->getMenuIcon(),
                        );
                    }
                }
            }
        }

        $this->writeCacheFile($cacheFile, sprintf('<?php return %s;', var_export($items, true)));
        $this->iterateMenu($menu, $items);
    }

    /**
     * @param ItemInterface $menu
     * @param array         $items
     */
    private function iterateMenu(ItemInterface $menu, array $items)
    {
        foreach ($items as $route => $item) {
            $this->addMenu($menu, $route, $item['name'], $item['icon']);
        }
    }

    /**
     * @param string $file
     * @param string $content
     */
    private function writeCacheFile($file, $content)
    {
        $tmpFile = tempnam(dirname($file), basename($file));
        if (false !== @file_put_contents($tmpFile, $content) && @rename($tmpFile, $file)) {
            @chmod($file, 0666 & ~umask());

            return;
        }

        throw new RuntimeException(sprintf('Failed to write cache file "%s".', $file));
    }
}
