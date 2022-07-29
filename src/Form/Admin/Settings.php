<?php

namespace PlaygroundDesign\Form\Admin;

use Laminas\Form\Form;
use Laminas\Form\Element;
use PlaygroundCore\Form\ProvidesEventsForm;
use Laminas\Mvc\I18n\Translator;
use Laminas\ServiceManager\ServiceManager;

class Settings extends ProvidesEventsForm
{
    /**
    * @var Laminas\ServiceManager\ServiceManager $serviceManager
    */
    protected $serviceManager;

    /**
    * __construct : permet de construire le formulaire qui peuplera l'entity company
    *
    * @param string $name
    * @param Laminas\ServiceManager\ServiceManager $serviceManager
    * @param use Laminas\Mvc\I18n\Translator $translator
    *
    */
    public function __construct($name = null, ServiceManager $serviceManager, Translator $translator)
    {
        parent::__construct($name);

        $this->setAttribute('enctype', 'multipart/form-data');

        $this->add(
            array(
                'name' => 'id',
                'type'  => 'Laminas\Form\Element\Hidden',
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

        $this->add(array(
            'type' => 'Laminas\Form\Element\Checkbox',
            'attributes' => array(
                'class' => 'switch-input',
            ),
            'name' => 'homeKeepClosedGamePosition',
            'options' => array(
                'label' => $translator->translate('Keep a closed game in its original position', 'playgroundgame'),
            ),
        ));

        $this->add(
            array(
                'name' => 'gReCaptchaUrl',
                'options' => array(
                    'label' => $translator->translate('Google ReCaptcha URL', 'playgrounddesign'),
                ),
                'attributes' => array(
                    'value' => 'https://www.google.com/recaptcha/api/siteverify',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'gReCaptchaKey',
                'options' => array(
                    'label' => $translator->translate('Google ReCaptcha Key', 'playgrounddesign'),
                ),
            )
        );

        $submitElement = new Element\Button('submit');
        $submitElement->setLabel($translator->translate('Upgrade', 'playgrounddesign'))
            ->setAttributes(array('type'  => 'submit'));

        $this->add($submitElement, array('priority' => -100));
    }
}
