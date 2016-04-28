### How to install AdminBundle (Symfonian Indonesia Admin Bundle)###

### Existing Project###

Add dependencies into your `composer.json` file

```lang=json
"doctrine/doctrine-fixtures-bundle": "dev-master",
"knplabs/knp-paginator-bundle": "dev-master",
"knplabs/knp-menu-bundle": "dev-master",
"symfonyid/core-bundle": "dev-master",
"symfonyid/symfony-bundle-plugins": "dev-master",
"friendsofsymfony/user-bundle": "dev-master",
"friendsofsymfony/jsrouting-bundle": "dev-master",
"symfonyid/admin-bundle": "^6.1"
```

**Composer Update**

Running composer update from your console or terminal

```lang=shell
composer update --prefer-dist -vvv
```

**Register Bundle**

Register the bundles into your `AppKernel.php` file

```lang=php
new FOS\UserBundle\FOSUserBundle(),
new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
new Knp\Bundle\MenuBundle\KnpMenuBundle(),
new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
new Symfonian\Indonesia\CoreBundle\SymfonianIndonesiaCoreBundle(),
new Symfonian\Indonesia\AdminBundle\SymfonianIndonesiaAdminBundle($this),
```

### New Project###

For new project, using `SKeleton` is very recomended.

Before install, create **empty database** first and then clone `Skeleton` repository

```lang=shell
git clone git@github.com:SymfonyId/Skeleton.git YourProject
```

Running composer update to download dependencies

```lang=shell
cd YourProject
composer update --prefer-dist -vvv
```

Just follow the instruction until complete and then run command below from your project root

```lang=shell
php bin/console siab:skeleton:setup
```

Running your server

```lang=shell
php bin/console server:run
```

Just open your browser

```lang=shell
localhost:8000/admin
```

[Next: Basic Usage](basic_usage.md)