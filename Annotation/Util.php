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

use Symfonian\Indonesia\AdminBundle\Configuration\ConfigurationInterface;

/**
 * @Annotation
 * @Target({"CLASS"})
 *
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class Util implements ConfigurationInterface
{
    private $autoComplete = array();
    private $datePicker = false;
    private $dateFormat = 'yyyy-mm-dd';
    private $htmlEditor = false;
    private $fileChooser = false;
    private $numeric = false;
    private $includeJavascript;
    private $includeRoute = array();
    private $uploadable;
    private $targetField;

    public function __construct(array $data = array())
    {
        if (isset($data['autoComplete'])) {
            if (!is_array($data['autoComplete'])) {
                throw new \InvalidArgumentException('autoComplete harus berupa array');
            }

            if (!(array_key_exists('route', $data['autoComplete']) && array_key_exists('targetSelector', $data['autoComplete']))) {
                throw new \InvalidArgumentException('route dan targetSelector harus diset');
            }

            $this->autoComplete['route'] = $data['autoComplete']['route'];
            $this->autoComplete['value_storage_selector'] = $data['autoComplete']['targetSelector'];
        }

        if (isset($data['datePicker'])) {
            $this->datePicker = true;
            if (isset($data['data_format'])) {
                $this->dateFormat = $data['date_format'];
            }
        }

        if (isset($data['htmlEditor'])) {
            $this->htmlEditor = (boolean) $data['htmlEditor'];
        }

        if (isset($data['fileChooser'])) {
            $this->fileChooser = (boolean) $data['fileChooser'];
        }

        if (isset($data['numeric'])) {
            $this->numeric = (boolean) $data['numeric'];
        }

        if (isset($data['includeJavascript'])) {
            $this->includeJavascript = $data['includeJavascript'];
        }

        if (isset($data['includeRoute'])) {
            if (!is_array($data['includeRoute'])) {
                $data['includeRoute'] = (array) $data['includeRoute'];
            }

            $this->includeRoute = $data['includeRoute'];
        }

        if (isset($data['uploadable'])) {
            if (isset($data['targetField'])) {
                $this->targetField = $data['targetField'];
            } else {
                $this->targetField = $data['uploadable'];
            }

            $this->uploadable = $data['uploadable'];
        }
    }

    /**
     * @return bool
     */
    public function isUseDatePicker()
    {
        return $this->datePicker ? true : false;
    }

    /**
     * @return string
     */
    public function getDateFormat()
    {
        return $this->dateFormat;
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
    public function getAutoComplete()
    {
        return $this->autoComplete;
    }

    /**
     * @return string
     */
    public function getIncludeJavascript()
    {
        return $this->includeJavascript;
    }

    /**
     * @return array
     */
    public function getIncludeRoute()
    {
        return $this->includeRoute;
    }

    /**
     * @return string
     */
    public function getUploadableField()
    {
        return $this->uploadable;
    }

    /**
     * @return string
     */
    public function getTargetField()
    {
        return $this->targetField;
    }

    /**
     * @param array $autoComplete
     */
    public function setAutoComplete(array $autoComplete)
    {
        $this->autoComplete = $autoComplete;
    }

    /**
     * @param bool $datePicker
     */
    public function setUseDatePicker($datePicker)
    {
        $this->datePicker = (bool) $datePicker;
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
     * @param string $includeJavascript
     */
    public function setIncludeJavascript($includeJavascript)
    {
        $this->includeJavascript = $includeJavascript;
    }

    /**
     * @param array $includeRoute
     */
    public function setIncludeRoute(array $includeRoute)
    {
        $this->includeRoute = $includeRoute;
    }

    /**
     * @param string $uploadable
     */
    public function setUploadableField($uploadable)
    {
        $this->uploadable = $uploadable;
    }

    /**
     * @param string $targetField
     */
    public function setTargetField($targetField)
    {
        $this->targetField = $targetField;
    }
}
