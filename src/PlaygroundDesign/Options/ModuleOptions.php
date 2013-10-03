<?php

namespace PlaygroundDesign\Options;

use Zend\Stdlib\AbstractOptions;

class ModuleOptions extends AbstractOptions
{
    /**
     * @var string
     */
    protected $actionEntityClass = 'PlaygroundDesign\Entity\Theme';

    /**
     * @var bool
     */
    protected $enableDefaultEntities = true;

    /**
     * Turn off strict options mode
     */
    protected $__strictMode__ = false;

    /**
     * drive path to theme media files
     */
    protected $media_path = 'design/';

    /**
     * url path to theme media files
     */
    protected $media_url = '/theme/images/screenshots';

    /**
     * @var string
     */
    protected $companyEntityClass = 'PlaygroundDesign\Entity\Company';

    /**
     * Set page entity class name
     *
     * @param $pageEntityClass
     * @return ModuleOptions
     */
    public function setCompanyEntityClass($companyEntityClass)
    {
        $this->companyEntityClass = $companyeEntityClass;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCompanyEntityClass()
    {
        return $this->companyEntityClass;
    }

    /*
     * @return \PlaygroundDesign\Options\ModuleOptions
     */
    public function setMediaPath($media_path)
    {
        $this->media_path = $media_path;

        return $this;
    }

    /**
     * @return string
     */
    public function getMediaPath()
    {
        return $this->media_path;
    }

    /**
     *
     * @param  string $media_url
     * @return \PlaygroundDesign\Options\ModuleOptions
     */
    public function setMediaUrl($media_url)
    {
        $this->media_url = $media_url;

        return $this;
    }

    /**
     * @return string
     */
    public function getMediaUrl()
    {
        return $this->media_url;
    }
}