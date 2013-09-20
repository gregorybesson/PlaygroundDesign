<?php
return array(
    'core_layout' => array(
        '{{type}}' => array(
            'layout' => 'layout/{{type}}',
            'modules' => array(
                'playgrounduser' => array(
                    'controllers' => array(
                        'playgrounduser{{type}}_login' => array(
                            'layout' => 'layout/{{type}}login',
                        ),
                    ),
                ),
            ),
        ),
    ),
);