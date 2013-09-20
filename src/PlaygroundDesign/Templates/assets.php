<?php 
return array(
    'assetic_configuration' => array(
        'modules' => array(
            'default_base' => array(
                'root_path' => array(
                    __DIR__ . '/../../../../design/{{type}}/{{package}}/{{theme}}/assets',
                ),
                'collections' => array(
                    '{{type}}_images' => array(
                        'assets' => array(
                            'images/**/*.jpg',
                            'images/**/*.png',
                            'images/**/*.gif',
                        ),
                        'options' => array(
                            'move_raw' => true,
                            'output' => '{{type}}/',
                        )
                    ),
                    
                    '{{type}}_fonts' => array(
                        'assets' => array(
                            'fonts/**/*.eot',
                            'fonts/**/*.svg',
                            'fonts/**/*.ttf',
                            'fonts/**/*.woff',
                        ),
                        'options' => array(
                            'move_raw' => true,
                            'output' => '{{type}}'
                        )
                    ),
                ),
            ),
        ),
    ),
);