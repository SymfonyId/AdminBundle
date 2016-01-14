###Cara Install SIAB (Symfonian Indonesia Admin Bundle)###

**Tambahkan ke composer.json**

Tambahkan dependencies berikut pada composer.json pada root project Anda

```lang=json
"knplabs/knp-paginator-bundle": "dev-master",
"knplabs/knp-menu-bundle": "dev-master",
"friendsofsymfony/user-bundle": "dev-master",
"friendsofsymfony/jsrouting-bundle": "dev-master",
"symfonyid/core-bundle": "dev-master",
"symfonyid/symfony-bundle-plugins": "dev-master",
"symfonyid/admin-bundle": "dev-master"
```

**Update composer**

Jalan command dibawah ini pada terminal/command prompt Anda

```lang=shell
composer update --prefer-dist
```

**Register Bundle**

Daftarkan bundles pada AppKernel.php agar dapat dikenali

```lang=php
new FOS\UserBundle\FOSUserBundle(),
new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
new Knp\Bundle\MenuBundle\KnpMenuBundle(),
new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
new Symfonian\Indonesia\CoreBundle\SymfonianIndonesiaCoreBundle(),
new Symfonian\Indonesia\AdminBundle\SymfonianIndonesiaAdminBundle(),
```

###Cara cepat###

Anda juga dapat menginstall dengan cara cepat dengan cloning repo SymfonyId Skeleton

```lang=shell
git clone git@github.com:SymfonyId/Skeleton.git
```

Kemudian jalankan perintah berikut dari root project, jalankan composer install

```lang=shell
composer update --prefer-dist
```

Setelah semuanya terinstall jalankan

```lang=shell
php bin/console siab:skeleton:setup
```

Kemudian Anda dapat menjalankan web server dengan perintah

```lang=shell
php bin/console server:run
```

Buka browser

```lang=shell
localhost:8000/admin
```