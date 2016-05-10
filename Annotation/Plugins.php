<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\Annotation;

use Symfonian\Indonesia\AdminBundle\Contract\ConfigurationInterface;

/**
 * @Annotation
 * @Target({"CLASS"})
 *
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class Plugins implements ConfigurationInterface
{
    /**
     * @var bool
     */
    private $htmlEditor = false;

    /**
     * @var bool
     */
    private $fileChooser = false;

    /**
     * @var bool
     */
    private $numeric = false;

    /**
     * @var bool
     */
    private $bulkInsert = false;

    /**
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        if (isset($data['htmlEditor'])) {
            $this->htmlEditor = (boolean) $data['htmlEditor'];
        }

        if (isset($data['fileChooser'])) {
            $this->fileChooser = (boolean) $data['fileChooser'];
        }

        if (isset($data['numeric'])) {
            $this->numeric = (boolean) $data['numeric'];
        }

        if (isset($data['bulkInsert'])) {
            $this->bulkInsert = (boolean) $data['bulkInsert'];
        }

        unset($data);
    }

    /**
     * @return bool
     */
    public function isUseHtmlEditor()
    {
        return $this->htmlEditor;
    }

    /**
     * @return bool
     */
    public function isUseNumeric()
    {
        return $this->numeric;
    }

    /**
     * @return bool
     */
    public function isUseFileChooser()
    {
        return $this->fileChooser;
    }

    /**
     * @return bool
     */
    public function isUseBulkInsert()
    {
        return $this->bulkInsert;
    }

    /**
     * @param bool $htmlEditor
     */
    public function setUseHtmlEditor($htmlEditor)
    {
        $this->htmlEditor = (bool) $htmlEditor;
    }

    /**
     * @param bool $fileChooser
     */
    public function setUseFileChooser($fileChooser)
    {
        $this->fileChooser = (bool) $fileChooser;
    }

    /**
     * @param bool $numeric
     */
    public function setUseNumeric($numeric)
    {
        $this->numeric = (bool) $numeric;
    }

    /**
     * @param bool $bulkInsert
     */
    public function setUseBulkInsert($bulkInsert)
    {
        $this->bulkInsert = (bool) $bulkInsert;
    }
}
