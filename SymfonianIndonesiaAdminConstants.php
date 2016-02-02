<?php

namespace Symfonian\Indonesia\AdminBundle;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin.
 */
class SymfonianIndonesiaAdminConstants
{
    const PRE_FORM_CREATE = 'siab.pre_form_create';
    const PRE_FORM_SUBMIT = 'siab.pre_form_submit';
    const PRE_FORM_VALIDATION = 'siab.pre_validation';
    const PRE_SAVE = 'siab.pre_save';
    const POST_SAVE = 'siab.post_save';
    const FILTER_LIST = 'siab.filter_query';
    const PRE_DELETE = 'siab.pre_delete';
    const PRE_SHOW = 'siab.pre_show';

    const ACTION_CREATE = 'ACTION_CREATE';
    const ACTION_UPDATE = 'ACTION_UPDATE';
    const ACTION_DELETE = 'ACTION_DELETE';
    const ACTION_READ = 'ACTION_READ';

    const GRID_ACTION_SHOW = 'GRID_ACTION_SHOW';
    const GRID_ACTION_EDIT = 'GRID_ACTION_EDIT';
    const GRID_ACTION_DELETE = 'GRID_ACTION_DELETE';

    const ENTITY_ALIAS = 'e';

    const TEMPLATE_CREATE = 'SymfonianIndonesiaAdminBundle:Crud:new.html.twig';
    const TEMPLATE_EDIT = 'SymfonianIndonesiaAdminBundle:Crud:new.html.twig';
    const TEMPLATE_SHOW = 'SymfonianIndonesiaAdminBundle:Crud:show.html.twig';
    const TEMPLATE_LIST = 'SymfonianIndonesiaAdminBundle:Crud:list.html.twig';
    const TEMPLATE_AJAX = 'SymfonianIndonesiaAdminBundle:Crud:list_template.html.twig';
    const TEMPLATE_DASHBOARD = 'SymfonianIndonesiaAdminBundle:Index:index.html.twig';
    const TEMPLATE_PROFILE = 'SymfonianIndonesiaAdminBundle:Index:profile.html.twig';
    const TEMPLATE_CHANGE_PASSWORD = 'SymfonianIndonesiaAdminBundle:Index:change_password.html.twig';
    const TEMPLATE_FORM = 'SymfonianIndonesiaAdminBundle:Form:fields.html.twig';
    const TEMPLATE_PAGINATION = 'SymfonianIndonesiaAdminBundle:Layout:pagination.html.twig';

    const CACHE_CONTROLLER_PATH = '/siab.controller.php';
    const CACHE_ENTITY_PATH = '/siab.entity.php';
}
