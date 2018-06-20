<?php

namespace PlaygroundDesign\Form\Admin;

use Zend\Form\Form;
use Zend\Form\Element;
use ZfcUser\Form\ProvidesEventsForm;
use Zend\Mvc\I18n\Translator;
use Zend\ServiceManager\ServiceManager;

class Company extends ProvidesEventsForm
{
    /**
    * @var Zend\ServiceManager\ServiceManager $serviceManager
    */
    protected $serviceManager;

    /**
    * __construct : permet de construire le formulaire qui peuplera l'entity company
    *
    * @param string $name
    * @param Zend\ServiceManager\ServiceManager $serviceManager
    * @param use Zend\Mvc\I18n\Translator $translator
    *
    */
    public function __construct($name = null, ServiceManager $serviceManager, Translator $translator)
    {
        parent::__construct($name);

        $this->setAttribute('enctype', 'multipart/form-data');

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
                'label' => $translator->translate('Company title', 'playgrounddesign'),
            ),
            'attributes' => array(
                'type' => 'text',
                'placeholder' => $translator->translate('Company title', 'playgrounddesign'),
                'required' => 'required'
            ),
            'validator' => array(
                new \Zend\Validator\NotEmpty(),
            )
        ));

        $this->add(array(
            'name' => 'address',
            'options' => array(
                'label' => $translator->translate('Company address', 'playgrounddesign'),
            ),
            'attributes' => array(
                'type' => 'textarea',
                'placeholder' => $translator->translate('Company address', 'playgrounddesign'),
                'required' => 'required'
            ),
            'validator' => array(
                new \Zend\Validator\NotEmpty(),
            )
        ));

        $this->add(array(
            'name' => 'phoneNumber',
            'options' => array(
                'label' => $translator->translate('Company phone number', 'playgrounddesign'),
            ),
            'attributes' => array(
                'type' => 'tel',
                'placeholder' => $translator->translate('Company phone number', 'playgrounddesign'),
                'required' => 'required'
            ),
            'validator' => array(
                new \Zend\Validator\NotEmpty(),
            )
        ));

        $this->add(array(
            'name' => 'facebookPage',
            'options' => array(
                'label' => $translator->translate('Company facebook page', 'playgrounddesign'),
            ),
            'attributes' => array(
                'type' => 'text',
                'placeholder' => $translator->translate('Company facebook page', 'playgrounddesign'),
            )
        ));

        $this->add(array(
            'name' => 'twitterAccount',
            'options' => array(
                'label' => $translator->translate('Company twitter account', 'playgrounddesign'),
            ),
            'attributes' => array(
                'type' => 'text',
                'placeholder' => $translator->translate('Company twitter account', 'playgrounddesign'),
            )
        ));

        // Adding an empty upload field to be able to correctly handle this on
        // the service side.
        $this->add(array(
            'name' => 'uploadMainImage',
            'attributes' => array(
                'type' => 'file'
            ),
            'options' => array(
                'label' => $translator->translate('Main image', 'playgrounddesign')
            )
        ));
        $this->add(array(
            'name' => 'mainImage',
            'type' => 'Zend\Form\Element\Hidden',
            'attributes' => array(
                'value' => ''
            )
        ));


        $submitElement = new Element\Button('submit');
        $submitElement->setLabel($translator->translate('Upgrade', 'playgrounddesign'))
                      ->setAttributes(array('type'  => 'submit'));

        $this->add($submitElement, array('priority' => -100));
    }
}
