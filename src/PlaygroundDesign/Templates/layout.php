<?php
return array(
    'core_layout' => array(
        '{{area}}' => array(
            'layout' => 'layout/{{area}}',
            'modules' => array(
                'playgrounduser' => array(
                    'controllers' => array(
                        'playgrounduser{{area}}_login' => array(
                            'layout' => 'layout/{{area}}login',
                        ),
                    ),
                ),
            ),
        ),
    ),
);
