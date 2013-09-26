<?php 
return array(
    'assetic_configuration' => array(
        'modules' => array(
            'default_base' => array(
                'root_path' => array(
                    __DIR__ . '/../../../../design/{{area}}/{{package}}/{{theme}}/assets',
                ),
                'collections' => array(
                    '{{area}}_images' => array(
                        'assets' => array(
                            'images/**/*.jpg',
                            'images/**/*.png',
                            'images/**/*.gif',
                        ),
                        'options' => array(
                            'move_raw' => true,
                            'output' => '{{area}}/',
                        )
                    ),
                    
                    '{{area}}_fonts' => array(
                        'assets' => array(
                            'fonts/**/*.eot',
                            'fonts/**/*.svg',
                            'fonts/**/*.ttf',
                            'fonts/**/*.woff',
                        ),
                        'options' => array(
                            'move_raw' => true,
                            'output' => '{{area}}'
                        )
                    ),
                ),
            ),
        ),
    ),
);