<?php

namespace PlaygroundDesign\Form\Admin;

use Zend\Form\Form;
use Zend\Form\Element;
use ZfcUser\Form\ProvidesEventsForm;
use Zend\Mvc\I18n\Translator;
use Zend\ServiceManager\ServiceManager;

class Settings extends ProvidesEventsForm
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

        $this->add(
            array(
                'name' => 'id',
                'type'  => 'Zend\Form\Element\Hidden',
                'attributes' => array(
                    'value' => 0,
                ),
            )
        );

        $this->add(
            array(
                'name' => 'homePagination',
                'options' => array(
                    'label' => $translator->translate('Home pagination', 'playgrounddesign'),
                ),
                'attributes' => array(
                    'type' => 'tel',
                    'placeholder' => $translator->translate('Home pagination', 'playgrounddesign'),
                ),
            )
        );

        $submitElement = new Element\Button('submit');
        $submitElement->setLabel($translator->translate('Upgrade', 'playgrounddesign'))
            ->setAttributes(array('type'  => 'submit'));

        $this->add($submitElement, array('priority' => -100));
    }
}