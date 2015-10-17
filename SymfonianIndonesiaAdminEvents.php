<?php

namespace Symfonian\Indonesia\AdminBundle;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin.
 */
class SymfonianIndonesiaAdminEvents
{
    const PRE_FORM_CREATE = 'symfonian_id.admin.pre_form_create_event';

    const PRE_FORM_SUBMIT = 'symfonian_id.admin.pre_form_submit_event';

    const PRE_FORM_VALIDATION = 'symfonian_id.admin.pre_form_validation_event';

    const PRE_SAVE = 'symfonian_id.admin.pre_save_event';

    const POST_SAVE = 'symfonian_id.admin.post_save_event';

    const FILTER_LIST = 'symfonian_id.admin.filter_list_event';

    const FILTER_RESULT = 'symfonian_id.admin.filter_result_event';

    const PRE_DELETE = 'symfonian_id.admin.pre_delete_event';

    const PRE_SHOW = 'symfonian_id.admin.pre_show_event';
}
