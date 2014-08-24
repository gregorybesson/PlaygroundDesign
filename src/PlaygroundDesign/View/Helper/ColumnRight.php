<?php
namespace PlaygroundDesign\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;

class ColumnRight extends AbstractHelper
{
    /**
     * $var string template used for view
     */
    protected $viewTemplate = 'playground-design/common/column_right.phtml';
    protected $rssUrl       = '';

    public function __construct()
    {
    }

    public function __invoke($options = array())
    {
        if (array_key_exists('template', $options) && $options['template'] != '') {
            $template = $options['template'];
        } else {
            $template = $this->viewTemplate;
        }

        if (array_key_exists('channel', $options) && $options['channel'] != '') {
            $channel = $options['channel'];
        } else {
            $channel = '';
        }

        /*if (array_key_exists('adserving', $options) && is_array($options['adserving'])) {
            $cat1 = $options['adserving']['cat1'];
            $cat2 = $options['adserving']['cat2'];
            $cat3 = $options['adserving']['cat3'];
        } else {*/
            $cat1 = 'playground';
            $cat2 = '';
            $cat3 = '';
        //}

        $vm = new ViewModel(array());
        $vm->setTemplate($template);
        $vm->setVariables(array(
            'cat1'   => $cat1,
            'cat2'   => $cat2,
            'cat3'   => $cat3,
            'rssUrl' => $this->getRssUrl(),
            'channel' => $channel,
        ));

        return $this->getView()->render($vm);
    }

    /**
     * @param  string      $viewTemplate
     * @return ColumnRight
     */
    public function setViewTemplate($viewTemplate)
    {
        $this->viewTemplate = $viewTemplate;

        return $this;
    }

    public function setRssUrl($rssUrl)
    {
        $this->rssUrl = $rssUrl;

        return $this;
    }

    public function getRssUrl()
    {
        return $this->rssUrl;
    }

}
