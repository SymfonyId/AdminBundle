<?php

namespace Symfonian\Indonesia\AdminBundle\Annotation;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin.
 */
use Symfonian\Indonesia\AdminBundle\Configuration\ConfigurationInterface;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class Util implements ConfigurationInterface
{
    protected $autoComplete = array();

    protected $datePicker;

    protected $htmlEditor;

    protected $fileChooser;

    protected $numeric;

    protected $includeJavascript;

    protected $includeRoute = array();

    protected $uploadable;

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
            $this->datePicker = (boolean) $data['datePicker'];
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
            $this->uploadable = $data['uploadable'];
        }
    }

    /**
     * @return bool
     */
    public function getAutoComplete()
    {
        return $this->autoComplete;
    }

    /**
     * @return bool
     */
    public function isUseDatePicker()
    {
        return $this->datePicker;
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
}
