<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\View;

use Symfonian\Indonesia\AdminBundle\SymfonianIndonesiaAdminConstants as Constants;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class Template
{
    /**
     * @var string
     */
    private $create = Constants::TEMPLATE_CREATE;

    /**
     * @var string
     */
    private $bulkCreate = Constants::TEMPLATE_BULK_CREATE;

    /**
     * @var string
     */
    private $edit = Constants::TEMPLATE_EDIT;

    /**
     * @var string
     */
    private $show = Constants::TEMPLATE_SHOW;

    /**
     * @var string
     */
    private $list = Constants::TEMPLATE_LIST;

    /**
     * Internal use only.
     *
     * @var string
     */
    private $ajaxTemplate = Constants::TEMPLATE_AJAX;

    /**
     * @return string
     */
    public function getCreate()
    {
        return $this->create;
    }

    /**
     * @param string $create
     */
    public function setCreate($create)
    {
        $this->create = $create;
    }

    /**
     * @return string
     */
    public function getBulkCreate()
    {
        return $this->bulkCreate;
    }

    /**
     * @param string $bulkCreate
     */
    public function setBulkCreate($bulkCreate)
    {
        $this->bulkCreate = $bulkCreate;
    }

    /**
     * @return string
     */
    public function getEdit()
    {
        return $this->edit;
    }

    /**
     * @param string $edit
     */
    public function setEdit($edit)
    {
        $this->edit = $edit;
    }

    /**
     * @return string
     */
    public function getShow()
    {
        return $this->show;
    }

    /**
     * @param string $show
     */
    public function setShow($show)
    {
        $this->show = $show;
    }

    /**
     * @return string
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * @param string $list
     */
    public function setList($list)
    {
        $this->list = $list;
    }

    /**
     * @return string
     */
    public function getAjaxTemplate()
    {
        return $this->ajaxTemplate;
    }

    /**
     * @param string $ajaxTemplate
     */
    public function setAjaxTemplate($ajaxTemplate)
    {
        $this->ajaxTemplate = $ajaxTemplate;
    }
}
