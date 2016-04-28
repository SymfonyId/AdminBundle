### How to Activate AdminBundle View Plugins###

**Plugin Activation**

To activate plugin is very easy. For example we want to activate WYSIWYG Editor

In your controller

```lang=php
use Symfonian\Indonesia\AdminBundle\Annotation\Plugins;

@Plugins(htmlEditor=true)
```

and then just add class `editor` in your form options

```lang=php
//other options
'attr' => array(
    'class' => 'editor',
),
//other options
```

**Plugin Available in @Plugins annotation**

+ htmlEditor for WYSIWYG Editor

+ fileChooser for File Upload Helper

+ numeric for only numeric input. same as htmlEditor, you need to add class `numeric` in your form type to activating this plugin

+ bulkInsert for bulk insert

[Next: How To Create Auto Complete Form With AdminBundle](autocomplete.md)