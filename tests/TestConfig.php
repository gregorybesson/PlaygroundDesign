<?php
return array(
    'modules' => array(
        'DoctrineModule',
        'DoctrineORMModule',
        'PlaygroundCore',
        'PlaygroundDesign',
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
