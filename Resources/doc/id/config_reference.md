```lang=yml
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
```