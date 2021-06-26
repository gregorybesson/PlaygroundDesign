<?php

namespace PlaygroundDesign\View\Helper;

use Laminas\View\Helper\AbstractHelper;

class FacebookUrl extends AbstractHelper
{

    /**
     * @var
     */
    protected $fbUrl = null;

    /**
     * @param $fbUrl
     */
    public function __construct($fbUrl)
    {
        $this->fbUrl = $fbUrl;
    }

    /**
     * @param $name
     * @param $options
     *
     * @return string
     */
    public function __invoke()
    {
        if ($this->fbUrl === null) {
            return false;
        }
        return $this->fbUrl;
    }
}
