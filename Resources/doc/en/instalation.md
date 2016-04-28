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

**Import Config**

```lang=yml
# Optional
assetic:
    bundles: ['AppBundle', 'SymfonianIndonesiaAdminBundle', 'FOSUserBundle']
    node: /usr/bin/nodejs #change to your path
    filters:
        cssrewrite:
            apply_to: '\.css$'
        uglifyjs2:
            bin: /usr/local/bin/uglifyjs #change to your path
            apply_to: '\.js$'
        uglifycss:
            bin: /usr/local/bin/uglifycss #change to your path
            apply_to: '\.css$'

# Required
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
    app_title: 'SKELETON'
    app_short_title: 'SFID'
    per_page: 10
    identifier: 'id'
    date_time_format: 'd-m-Y' #php date time format
    profile_fields: ['full_name', 'username', 'email', 'roles', 'enabled']
    filter: ['name']
    translation_domain: 'AppBundle'
    user:
        form_class: symfonian_id.admin.user_form
        entity_class: AppBundle\Entity\User
    themes:
        dashboard: 'SymfonianIndonesiaAdminBundle:Index:index.html.twig'
        form_theme: 'SymfonianIndonesiaAdminBundle:Form:fields.html.twig'
        pagination: 'SymfonianIndonesiaAdminBundle:Layout:pagination.html.twig'

fos_user:
    db_driver: orm
    firewall_name: main
    user_class: AppBundle\Entity\User
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