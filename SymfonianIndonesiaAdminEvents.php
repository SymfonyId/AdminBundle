<?php
namespace Symfonian\Indonesia\AdminBundle;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */
class SymfonianIndonesiaAdminEvents
{
    const PRE_FORM_CREATE_EVENT = 'symfonian_id.admin.pre_form_create_event';//GetFormResponseEvent

    const PRE_FORM_SUBMIT_EVENT = 'symfonian_id.admin.pre_form_submit_event';//GetFormResponseEvent

    const PRE_FORM_VALIDATION_EVENT = 'symfonian_id.admin.pre_form_validation_event';//GetResponseEvent

    const PRE_SAVE_EVENT = 'symfonian_id.admin.pre_save_event';//GetEntityResponseEvent

    const POST_SAVE_EVENT = 'symfonian_id.admin.post_save_event';//GetEntityEvent

    const FILTER_LIST_EVENT = 'symfonian_id.admin.filter_list_event';//GetQueryEvent

    const PRE_DELETE_EVENT = 'symfonian_id.admin.pre_delete_event';//GetEntityResponseEvent

    const PRE_SHOW_EVENT = 'symfonian_id.admin.pre_show_event';//GetDataEvent
}
