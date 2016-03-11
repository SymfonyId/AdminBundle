<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class SymfonianIndonesiaAdminConstants
{
    const PRE_FORM_CREATE = 'siab.pre_form_create';
    const PRE_FORM_SUBMIT = 'siab.pre_form_submit';
    const PRE_SAVE = 'siab.pre_save';
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
    const SESSION_SORTED_NAME = 'siab_sorted';

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

    const CACHE_DIR = 'siab';
}
