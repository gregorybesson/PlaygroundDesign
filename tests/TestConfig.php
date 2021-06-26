<?php
return array(
    'modules' => array(
        'DoctrineModule',
        'DoctrineORMModule',
        'PlaygroundCore',
        'PlaygroundDesign',
        'Laminas\Router',
        'Laminas\Navigation',
        'Laminas\I18n',
        'Laminas\Mvc\Plugin\FlashMessenger',
    ),
    'module_listener_options' => array(
        'config_glob_paths'    => array(
            '../../config/autoload/{,*.}{global,local,testing}.php',
            './config/{,*.}{testing}.php',
        ),
        'module_paths' => array(
            'module',
            'vendor',
        ),
    ),
);
