##Annotation Reference##

###@Crud###

Annotation ini digunakan untuk memetakan entity, field yang akan ditampilkan, form, dll yang berhubungan dengan CRUD.

```lang=php
use Symfonian\Indonesia\AdminBundle\Annotation\Crud;

/**
 * @Crud("AppBundle\Entity\IdName", showFields={"name"})
 */
class IdNameController extends CrudController
{
}
```

- entity: property entity digunakan untuk memanipulasi entity
- create : property add digunakan untuk memanipulasi add template (bukan form tapi view/twig)
- edit: property edit digunakan untuk memanipulasi edit template (bukan form tapi view/twig)
- list: property list digunakan untuk memanipulasi list template
- show: property show digunakan untuk memanipulasi show template
- form: property form digunakan untuk memanipulasi form
- showFields: property showFields digunakan untuk memanipulasi field-field dari entity yang akan ditampilkan
- allowCreate: property allowCreate digunakan untuk membatasi akses create (default true)
- allowEdit: property allowEdit digunakan untuk membatasi akses edit (default true)
- allowShow: property allowShow digunakan untuk membatasi akses show (default true)
- allowDelete: property allowDelete digunakan untuk membatasi akses delete (default true)

###@Page###
Annotation ini digunakan untuk memetakan judul dan deskripsi halaman.

```lang=php
use Symfonian\Indonesia\AdminBundle\Annotation\Page;

/**
 * @Page("Page Title", description="Page Description")
 */
class IdNameController extends CrudController
{
}
```

- title: property title digunakan untuk memanipulasi page title
- description: property description digunakan untuk memanipulasi page description

###@Grid###

```lang=php
use Symfonian\Indonesia\AdminBundle\Annotation\Grid;

/**
 * @Grid({"name"}, filters={"name"})
 */
class IdNameController extends CrudController
{
}
```

- columns: property columns digunakan untuk mapping grid columns
- filters: property filters digunakan untuk mendaftarkan grid filters
- normalizeFilter: property normalizeFilter digunkan untuk mengubah input filter, jika true maka input filter akan diubah jadi uppercase (default: false)
- formatNumber: property formatNumber digunakan untuk memformat record tipe number pada grid (default: true)