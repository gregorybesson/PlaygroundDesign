<?php

namespace PlaygroundDesign\Options;

class ModuleOptions
{
    /**
     * @var string
     */
    protected $actionEntityClass = 'PlaygroundDesign\Entity\Skin';

    /**
     * @var bool
     */
    protected $enableDefaultEntities = true;

    /**
     * Turn off strict options mode
     */
    protected $__strictMode__ = false;

    /**
     * drive path to game media files
     */
    protected $media_path = 'public/media/skin';

    /**
     * url path to game media files
     */
    protected $media_url = 'media/skin';

    /**
     * @var string
     */
    protected $partnerMapper = 'PlaygroundDesign\Mapper\Skin';

    /**
     * Set media path
     *
     * @param  string    $media_path
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
