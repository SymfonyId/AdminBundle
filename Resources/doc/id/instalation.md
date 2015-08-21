###Cara Install SIAB (Symfonian Indonesia Admin Bundle)###

**Tambahkan ke composer.json**

```lang=json
"knplabs/knp-paginator-bundle": "2.4.*@dev",
"knplabs/knp-menu-bundle": "~2",
"friendsofsymfony/user-bundle": "~2.0@dev",
"friendsofsymfony/jsrouting-bundle": "dev-master",
"symfonyid/admin-bundle": "dev-master",
"symfonyid/core-bundle": "dev-master",
"symfonyid/symfony-bundle-plugins": "dev-master"
```

**Update composer**

```lang=shell
composer update --prefer-dist
```

**Register Bundle**