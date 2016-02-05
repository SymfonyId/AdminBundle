###Cara Penggunaan###

- Buat Entity

```lang=php
<?php
namespace AppBundle\Entity;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: http://blog.khodam.org
 */

use Doctrine\ORM\Mapping as ORM;
use Symfonian\Indonesia\AdminBundle\Grid\Column;
use Symfonian\Indonesia\AdminBundle\Grid\Filter;
use Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Model\EntityInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="siab_idname")
 */
class IdName implements EntityInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Column()
     * @Filter()
     * @ORM\Column(name="program_name", type="string", length=77)
     */
    protected $name;

    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = strtoupper($name);

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }
}
```

- Buat CRUD Controllernya

```lang=php
<?php
namespace AppBundle\Controller;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: http://blog.khodam.org
 */

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfonian\Indonesia\AdminBundle\Annotation\Crud;
use Symfonian\Indonesia\AdminBundle\Annotation\Page;
use Symfonian\Indonesia\AdminBundle\Controller\CrudController;

/**
 * @Route("/contoh")
 *
 * @Page("Sekedar Contoh", description="Ini adalah sekedar contoh CRUD menggunakan SIAB")
 * @Crud("AppBundle\Entity\IdName", form="AppBundle\Form\IdNameType", showFields={"name"})
 */
class IdNameController extends CrudController
{
    protected function getClassName()
    {
        return __CLASS__;
    }
}
```

- Daftarkan menunya

```lang=php
<?php
namespace AppBundle\Menu;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: http://blog.khodam.org
 */

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Router;
use Symfonian\Indonesia\AdminBundle\Menu\Builder as BaseMenu;
use Knp\Menu\MenuItem;

class Builder extends BaseMenu
{
    public function __construct(Router $router, ContainerInterface $container)
    {
        parent::__construct($router, $container);
    }

    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = parent::mainMenu($factory, $options);

        $this->addIdNameMenu($menu);

        return $menu;
    }

    protected function addIdNameMenu(MenuItem $menu)
    {
        $menu->addChild('IdName', array(
            'uri' => '#',
            'label' => '<i class="fa fa-building"></i> <span>IdName</span><i class="fa fa-angle-double-left pull-right"></i></a>',
            'extras' => array('safe_label' => true),
            'attributes' => array(
                'class' => 'treeview'
            )
        ));
        $menu['IdName']->setChildrenAttribute('class', 'treeview-menu');

        $menu['IdName']->addChild('AddIdName', array(
            'label' => 'Tambah IdName',
            'route' => 'app_idname_new',
            'attributes' => array(
                'class' => 'treeview'
            )
        ));

        $menu['IdName']->addChild('ListIdName', array(
            'label' => 'Daftar IdName',
            'route' => 'app_idname_list',
            'attributes' => array(
                'class' => 'treeview'
            )
        ));

        return $menu;
    }
}
```

- Buat Confignya

```lang=yaml
assetic:
    bundles: ['OroriStockBundle', 'SymfonianIndonesiaAdminBundle', 'FOSUserBundle']
    node: /usr/bin/nodejs #change to your path
    filters:
        cssrewrite: ~
        uglifyjs2:
            bin: /usr/local/bin/uglifyjs #change to your path
        uglifycss:
            bin: /usr/local/bin/uglifycss #change to your path

framework:
    translator: { fallbacks: ["%locale%"] }

knp_paginator:
    page_range: 5
    default_options:
        page_name: page
        sort_field_name: sort
        sort_direction_name: direction
        distinct: true

knp_menu:
    twig:
        template: knp_menu.html.twig
    templating: false
    default_renderer: twig

symfonyid_core:
    micro_cache:
        cache_lifetime: 5

symfonyid_admin:
    app_title: 'ORORI STOCK SYSTEM'
    app_short_title: 'OSS'
    per_page: 10
    identifier: 'id'
    date_time_format: 'd-m-Y' #php date time format
    menu: app_main_menu
    profile_fields: ['full_name', 'username', 'email', 'roles', 'enabled']
    filter: ['name']
    translation_domain: 'SymfonianIndonesiaAdminBundle'
    user:
        form_class: symfonian_id.admin.user_form
        entity_class: Orori\StockBundle\Entity\User
    themes:
        dashboard: 'SymfonianIndonesiaAdminBundle:Index:index.html.twig'
        form_theme: 'SymfonianIndonesiaAdminBundle:Form:fields.html.twig'
        pagination: 'SymfonianIndonesiaAdminBundle:Layout:pagination.html.twig'

fos_user:
    db_driver: orm
    firewall_name: main
    user_class: Orori\StockBundle\Entity\User
```

Jangan lupa untuk mendaftarkan di `app/config/config.yml`

- Buat service untuk menunya

```lang=yaml
services:
    app.menu:
        class: AppBundle\Menu\Builder
        arguments:
            - '@router'
            - '@translator'
            - '@security.authorization_checker'
            - '%symfonian_id.admin.translation_domain%'

    app.main_menu:
        class: Knp\Menu\MenuItem
        factory:
            - '@app.menu'
            - mainMenu
        arguments:
            - '@knp_menu.factory'
            - []
        tags:
            - { name: knp_menu.menu, alias: app_main_menu }
```