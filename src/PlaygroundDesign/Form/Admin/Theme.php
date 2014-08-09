<?php

namespace PlaygroundDesign\Form\Admin;

use Zend\Form\Element;
use ZfcBase\Form\ProvidesEventsForm;
use Zend\Mvc\I18n\Translator;
use Zend\ServiceManager\ServiceManager;

class Theme extends ProvidesEventsForm
{
    /**
    * @var Zend\ServiceManager\ServiceManager $serviceManager
    */
    protected $serviceManager;

    /**
    * __construct : permet de construire le formulaire qui peuplera l'entity theme
    *
    * @param string $name
    * @param Zend\ServiceManager\ServiceManager $serviceManager
    * @param Zend\Mvc\I18n\Translator $translator
    *
    */
    public function __construct($name = null, ServiceManager $serviceManager, Translator $translator)
    {
        parent::__construct($name);

        $this->setAttribute('enctype','multipart/form-data');

        $this->add(array(
            'name' => 'id',
            'type'  => 'Zend\Form\Element\Hidden',
            'attributes' => array(
                'value' => 0,
            ),
        ));

        $this->add(array(
            'name' => 'title',
            'options' => array(
                'label' => $translator->translate('theme_title', 'playgrounddesign'),
            ),
            'attributes' => array(
                'type' => 'text',
                'placeholder' => $translator->translate('theme_title', 'playgrounddesign'),
                'required' => 'required'
            ),
            'validator' => array(
                new \Zend\Validator\NotEmpty(),
            )
        ));

        $this->add(array(
            'name' => 'uploadImage',
            'attributes' => array(
                'type'  => 'file',
            ),
            'options' => array(
                'label' => $translator->translate('theme_image', 'playgrounddesign'),
            ),
        ));
        $this->add(array(
            'name' => 'image',
            'type'  => 'Zend\Form\Element\Hidden',
            'attributes' => array(
                    'value' => '',
            ),
        ));

        $this->add(array(
            'name' => 'theme_path',
            'options' => array(
                'label' => $translator->translate('theme_path', 'playgrounddesign'),
            ),
        ));

        $this->add(array(
            'name' => 'area',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => $translator->translate('theme_area', 'playgrounddesign'),
                'required' => 'required'
            ),
        ));

        $this->add(array(
            'name' => 'package',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => $translator->translate('theme_package', 'playgrounddesign'),
                'required' => 'required'
            ),
        ));

        $this->add(array(
            'name' => 'theme',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => $translator->translate('theme_theme', 'playgrounddesign'),
                'required' => 'required'
            ),
        ));

        $this->add(array(
            'name' => 'author',
            'options' => array(
                'label' => $translator->translate('theme_author', 'playgrounddesign'),
            ),
            'attributes' => array(
                'type' => 'text',
                'placeholder' => $translator->translate('theme_author', 'playgrounddesign'),
                'required' => 'required'
            ),
        ));


        $submitElement = new Element\Button('submit');
        $submitElement->setAttributes(array('type'  => 'submit'));

        $this->add($submitElement, array('priority' => -100));
    }
}
