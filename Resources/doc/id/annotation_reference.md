##Annotation Reference##

###@Crud###

Annotation ini digunakan untuk memetakan entity, field yang akan ditampilkan, form, dll yang berhubungan dengan CRUD.

```lang=php
use Symfonian\Indonesia\AdminBundle\Annotation\Schema\Crud;

/**
 * @Crud("AppBundle\Entity\IdName", showFields={"name"})
 */
class IdNameController extends CrudController
{
}
```

- add : property add digunakan untuk memanipulasi add template (bukan form tapi view/twig)
- edit: property edit digunakan untuk memanipulasi edit template (bukan form tapi view/twig)
- list: property list digunakan untuk memanipulasi list template
- show: property show digunakan untuk memanipulasi show template
- ajaxTemplate: property ajaxTemplate digunakan untuk memanipulasi ajax template pada list/pagination
- form: property form digunakan untuk memanipulasi form
- entity: property entity digunakan untuk memanipulasi entity
- showFields: property showFields digunakan untuk memanipulasi field-field dari entity yang akan ditampilkan
