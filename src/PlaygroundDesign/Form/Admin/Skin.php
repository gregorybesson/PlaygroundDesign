<?php

namespace PlaygroundDesign\Form\Admin;

use Zend\Form\Form;
use Zend\Form\Element;
use ZfcBase\Form\ProvidesEventsForm;
use Zend\I18n\Translator\Translator;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\ServiceManager\ServiceManager;

class Skin extends ProvidesEventsForm
{
    protected $serviceManager;

    public function __construct($name = null, ServiceManager $serviceManager, Translator $translator)
    {
        parent::__construct($name);

        $entityManager = $serviceManager->get('playgrounddesign_doctrine_em');

        parent::__construct();
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
                'label' => $translator->translate('skin_title', 'playgrounddesign'),
            ),
            'attributes' => array(
                'type' => 'text',
                'placeholder' => $translator->translate('skin_title', 'playgrounddesign'),
            ),
        ));

        $this->add(array(
            'name' => 'uploadImage',
            'attributes' => array(
                'type'  => 'file',
            ),
            'options' => array(
                'label' => $translator->translate('skin_image', 'playgrounddesign'),
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
            'name' => 'skin_path',
            'options' => array(
                'label' => $translator->translate('skin_path', 'playgrounddesign'),
            ),
        ));

        $this->add(array(
            'name' => 'type',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => $translator->translate('skin_type', 'playgrounddesign'),
            ),
        ));

        $this->add(array(
            'name' => 'package',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => $translator->translate('skin_package', 'playgrounddesign'),
            ),
        ));

        $this->add(array(
            'name' => 'theme',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => $translator->translate('skin_theme', 'playgrounddesign'),
            ),
        ));

        $this->add(array(
            'name' => 'author',
            'options' => array(
                'label' => $translator->translate('skin_author', 'playgrounddesign'),
            ),
            'attributes' => array(
                'type' => 'text',
                'placeholder' => $translator->translate('skin_author', 'playgrounddesign'),
            ),
        ));


        $submitElement = new Element\Button('submit');
        $submitElement->setLabel($translator->translate('Create', 'playgrounddesign'))
                      ->setAttributes(array('type'  => 'submit'));

        $this->add($submitElement, array('priority' => -100));
    }
}
