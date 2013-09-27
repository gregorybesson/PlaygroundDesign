<?php
return array(

    'doctrine' => array(
        'driver' => array(
            'playgrounddesign_entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => __DIR__ . '/../src/PlaygroundDesign/Entity'
            ),

            'orm_default' => array(
                'drivers' => array(
                    'PlaygroundDesign\Entity'  => 'playgrounddesign_entity'
                )
            )
        )
    ),

    'data-fixture' => array(
        'PlaygroundDesign_fixture' => __DIR__ . '/../src/PlaygroundDesign/DataFixtures/ORM',
    ),


    'service_manager' => array(
        'factories' => array(
            // this definition has to be done here to override Wilmogrod Assetic declaration
            'AsseticBundle\Service' => 'PlaygroundDesign\Assetic\ServiceFactory',
            'translator' => 'Zend\Mvc\Service\TranslatorServiceFactory',
            'nav' => 'Zend\Navigation\Service\DefaultNavigationFactory',
        ),
    ),

    'assetic_configuration' => array(
        'buildOnRequest' => true,
        'debug' => false,
        'acceptableErrors' => array(
            //defaults
            \Zend\Mvc\Application::ERROR_CONTROLLER_NOT_FOUND,
            \Zend\Mvc\Application::ERROR_CONTROLLER_INVALID,
            \Zend\Mvc\Application::ERROR_ROUTER_NO_MATCH,
            //allow assets when authorisation fails when using the BjyAuthorize module
            \BjyAuthorize\Guard\Route::ERROR,
        ),

        'webPath' => __DIR__ . '/../../../../public',
        'cacheEnabled' => false,
        'cachePath' => __DIR__ . '/../../../../data/cache',
        'modules' => array(
            'admin' => array(
                # module root path for your css and js files
                'root_path' => array(
                        __DIR__ . '/../view/admin/assets',
                ),
                # collection of assets
                'collections' => array(
                    'admin_css' => array(
                        'assets' => array(
                            'bootstrap.min.css'              => 'css/bootstrap.min.css',
                            'bootstrap-responsive.min.css'   => 'css/bootstrap-responsive.min.css',
                            'ie8.css'                        => 'css/ie8.css',
                            'ie.css'                         => 'css/ie.css',
                            'administration.css'             => 'css/administration.css',
                            'jquery-ui.css'                  => 'http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css',
                            'datepicker.css'                 => 'css/lib/datepicker.css',
                            'jquery-ui-timepicker-addon.css' => 'css/lib/jquery-ui-timepicker-addon.css',
                        ),
                        'filters' => array(),
                        'options' => array(
                            'output' => 'zfcadmin/css/main'
                        ),
                    ),
                    'head_admin_js' => array(
                        'assets' => array(
                            'jquery-1.9.0.min.js'           => 'js/lib/jquery-1.9.0.min.js',
                            'admin.js'                      => 'js/admin/admin.js',
                            'jquery-validate.js'            => 'js/lib/jquery.validate.min.js',
                            'jquery-ui.min.js'              => 'js/lib/jquery-ui.min.js',
                            'bootstrap.min.js'              => 'js/lib/bootstrap.min.js',
                            'jquery-ui-timepicker-addon.js' => 'js/lib/jquery-ui-timepicker-addon.js',
                            'json.js'                       => 'js/lib/json.js',
                            'drag.js'                       => 'js/admin/drag.js',
                        ),
                        'filters' => array(),
                        'options' => array(
                            'output' => 'zfcadmin/js/head_main',
                        ),
                    ),
                    'admin_images' => array(
                        'assets' => array(
                            'images/**/*.jpg',
                            'images/**/*.png',
                            'images/**/*.gif',
                        ),
                        'options' => array(
                            'move_raw' => true,
                            'output' => 'zfcadmin',
                        )
                    ),
                    'admin_jquery_ui_images' => array(
                        'assets' => array(
                            'css/images/*.jpg',
                            'css/images/*.png',
                            'css/images/*.gif',
                        ),
                        'options' => array(
                            'move_raw' => true,
                            'output' => 'zfcadmin',
                        )
                    ),
                    'admin_fonts' => array(
                        'assets' => array(
                            'fonts/**/*.eot',
                            'fonts/**/*.svg',
                            'fonts/**/*.ttf',
                            'fonts/**/*.woff',
                        ),
                        'options' => array(
                            'move_raw' => true,
                            'output' => 'zfcadmin',
                        )
                    ),
                    'admin_ckeditor' => array(
                        'assets' => array(
                            'js/lib/ckeditor/*',
                            'js/lib/ckeditor/**/*',
                            'js/lib/ckeditor/**/**/*',
                            'js/lib/ckeditor/**/**/**/*',
                            'js/ckeditor-custom/*',
                            'js/ckeditor-custom/**/*',
                            'css/ckeditor-custom/*',
                        ),
                        'options' => array(
                            'move_raw' => true,
                            'output' => 'zfcadmin',
                        )
                    ),
                    'admin_jquery_min_map' => array(
                        'assets' => array(
                            'js/lib/jquery-1.9.0.min.map',
                        ),
                        'options' => array(
                            'move_raw' => true,
                            'output' => 'zfcadmin',
                        )
                    ),
                    'admin_formgen' => array(
                        'assets' => array(
                            'js/form/*',
                        ),
                        'options' => array(
                            'move_raw' => true,
                            'output' => 'zfcadmin',
                        )
                    ),
                ),
            ),
            'frontend' => array(
                # module root path for your css and js files
                'root_path' => array(
                    __DIR__ . '/../view/frontend/assets',
                ),
                # collection of assets
                'collections' => array(
                    'frontend_css' => array(
                        'assets' => array(
                            'ie7.css'                => 'css/ie7.css',
                            'ie8.css'                => 'css/ie8.css',
                            'ie.css'                 => 'css/ie.css',
                            'styles.css'             => 'css/styles.css',
                            'uniform.default.css'    => 'css/uniform.default.css',
                        ),
                        'filters' => array(),
                        'options' => array(
                            'output' => 'frontend/css/main'
                        ),
                    ),
                    'head_frontend_js' => array(
                        'assets' => array(
                            //'html5.js' => 'js/html5.js',
                            //'pie.js' => 'js/lib/pie.js',
                            //'selectivizr-min.js' => 'js/lib/selectivizr-min.js',
                            'jquery-1.9.0.min.js' => 'js/lib/jquery-1.9.0.min.js',
                            'jquery-ui.js' => 'http://code.jquery.com/ui/1.10.2/jquery-ui.js',
                            'bowser.min.js' => 'js/lib/bowser.min.js',
                            'loader.js' => 'js/loader.js',
                            'popin.js' => 'js/popin.js',
                            'jscrollpane.js' => 'js/lib/jscrollpane.js',
                            'mousewheel.js' => 'js/lib/mousewheel.js',
                            'jquery.validate.min.js'=> 'js/lib/jquery.validate.min.js',
                            'jquery.nivo.slider.js' => 'js/lib/jquery.nivo.slider.js',
                            'jquery.uniform-2.0.js' => 'js/lib/jquery.uniform-2.0.js',
                            'jquery.limit-1.2.source.js' => 'js/lib/jquery.limit-1.2.source.js',
                            'wScratchpad.js' => 'js/lib/wScratchPad.js',
                            'jquery.timer.js' => 'js/lib/jquery.timer.js',
                            'sniffer.js' => 'js/sniffer.js',
                            'functions.js' => 'js/functions.js',
                            'script.js' => 'js/script.js',
                            'users.js' => 'js/users.js',
                            'share.js' => 'js/share.js',
                            'games.js' => 'js/games.js',
                            'bootstrap.min.js' => 'js/bootstrap.min.js',
                        ),
                        'filters' => array(),
                        'options' => array(
                            'output' => 'frontend/js/head_main',
                        ),
                    ),
                    'frontend_images' => array(
                        'assets' => array(
                            'images/**/*.png',
                            'images/**/*.jpg',
                        ),
                        'options' => array(
                            'move_raw' => true,
                            'output' => 'frontend',
                        )
                    ),
                    'frontend_fonts' => array(
                        'assets' => array(
                            'fonts/**/*.eot',
                            'fonts/**/*.svg',
                            'fonts/**/*.ttf',
                            'fonts/**/*.woff',
                        ),
                        'options' => array(
                            'move_raw' => true,
                            'output' => 'frontend'
                        )
                    ),
                ),
            ),
        ),

        'routes' => array(
            'admin.*' => array(
                '@admin_css',
                '@head_admin_js',
            ),
            'frontend.*' => array(
                '@frontend_css',
                '@head_frontend_js',
            ),
        ),
    ),

  'router' => array(
    'routes' => array(
      'frontend' => array(
        'type' => 'PlaygroundCore\Mvc\Router\Http\RegexSlash',
        'options' => array(
          'regex'    => '\/(?<channel>(embed|facebook|platform|mobile)+)?\/?',
          'defaults' => array(
            'controller' => 'PlaygroundDesign\Controller\Dashboard',
            'action'     => 'index',
          ),
          'spec' => '/%channel%/',
        ),
        'may_terminate' => true,
      ),
      'admin' => array(
        'type' => 'Literal',
        'priority' => 1000,
        'options' => array(
          'route'    => '/admin',
          'defaults' => array(
            'controller' => 'PlaygroundDesign\Controller\Dashboard',
            'action'     => 'index',
          ),
        ),
        'may_terminate' => true,
        'child_routes' => array(
          'dashboard' => array(
            'type' => 'literal',
            'options' => array(
              'route'    => '/dashboard',
              'defaults' => array(
                'controller' => 'PlaygroundDesign\Controller\Dashboard',
                'action'     => 'index',
              ),
            ),
          ),
          'playgrounddesign_companyadmin' => array(
            'type' => 'Literal',
            'options' => array(
              'route'    => '/company',
              'defaults' => array(
                'controller' => 'PlaygroundDesign\Controller\CompanyAdmin',
                'action'     => 'index',
              ),
            ),
            'may_terminate' => true,
          ),
          'playgrounddesign_themeadmin' => array(
            'type' => 'Literal',
            'options' => array(
                'route'    => '/theme',
                'defaults' => array(
                    'controller' => 'PlaygroundDesign\Controller\ThemeAdmin',
                    'action'     => 'list',
                ),
            ),
            'may_terminate' => true,
          ),
          'playgrounddesign_themeadmin_new' => array(
            'type' => 'Literal',
            'options' => array(
                'route'    => '/theme/new',
                'defaults' => array(
                    'controller' => 'PlaygroundDesign\Controller\ThemeAdmin',
                    'action'     => 'new',
                ),
            ),
            'may_terminate' => true,
          ),
          'playgrounddesign_themeadmin_edit' => array(
            'type' => 'Segment',
            'options' => array(
                'route'    => '/theme/[:themeId]/update',
                'constraints' => array(
                    'id' => '[0-9]+',
                ),
              'defaults' => array(
                'controller' => 'PlaygroundDesign\Controller\ThemeAdmin',
                'action'     => 'edit',
              ),
            ),
            'may_terminate' => true,
          ),
          'playgrounddesign_themeadmin_delete' => array(
            'type' => 'Segment',
            'options' => array(
                'route'    => '/theme/[:themeId]/delete',
                'constraints' => array(
                    'id' => '[0-9]+',
                ),
              'defaults' => array(
                'controller' => 'PlaygroundDesign\Controller\ThemeAdmin',
                'action'     => 'delete',
              ),
            ),
            'may_terminate' => true,
          ),
          'playgrounddesign_themeadmin_activate' => array(
            'type' => 'Segment',
            'options' => array(
                'route'    => '/theme/[:themeId]/activate',
                'constraints' => array(
                    'id' => '[0-9]+',
                ),
              'defaults' => array(
                'controller' => 'PlaygroundDesign\Controller\ThemeAdmin',
                'action'     => 'activate',
              ),
            ),
            'may_terminate' => true,
          ),
          'system' => array(
            'type' => 'literal',
            'options' => array(
              'route'    => '/system',
              'defaults' => array(
                'controller' => 'PlaygroundDesign\Controller\System',
                'action'     => 'index',
              ),
            ),
            'may_terminate' => true,
            'child_routes' => array(
              'modules' => array(
                'type' => 'literal',
                'options' => array(
                  'route'    => '/modules',
                  'defaults' => array(
                    'controller' => 'PlaygroundDesign\Controller\System',
                    'action'     => 'modules',
                  ),
                )
              ),
              'settings' => array(
                'type' => 'literal',
                'options' => array(
                  'route'    => '/settings',
                  'defaults' => array(
                    'controller' => 'PlaygroundDesign\Controller\System',
                    'action'     => 'settings',
                  ),
                )
              ),
            )
          ),
        ),
      ),
    ),
  ), 

    'core_layout' => array(
        'admin' => array(
            'layout' => 'layout/admin',
        ),
        'frontend' => array(
            'layout' => 'layout/layout',
        ),
    ),


    'controllers' => array(
        'invokables' => array(
            'PlaygroundDesign\Controller\Dashboard' => 'PlaygroundDesign\Controller\DashboardController',
            'PlaygroundDesign\Controller\System'    => 'PlaygroundDesign\Controller\SystemController',
            'PlaygroundDesign\Controller\CompanyAdmin' => 'PlaygroundDesign\Controller\CompanyAdminController',
            'PlaygroundDesign\Controller\themeAdmin' => 'PlaygroundDesign\Controller\ThemeAdminController',
        ),
    ),

    'translator' => array(
        'locale' => 'fr_FR',
        'translation_file_patterns' => array(
            array(
                'type'         => 'phpArray',
                'base_dir'     => __DIR__ . '/../language',
                'pattern'      => '%s.php',
                'text_domain'  => 'playgrounddesign'
            ),
        ),
    ),

    'navigation' => array(
        'admin' => array(
            'home' => array(
                'label' => 'Accueil',
                'route' => 'admin',
                'order' => -100,
                'resource' => 'design',
                'privilege' => 'edit',
            ),
             'playgroundconfigurationadmin' => array(
                'order' => 100,
                'label' => 'Configuration',
                'route' => 'admin/playgrounddesign_companyadmin',
                'resource' => 'design',
                'privilege' => 'system',
                'pages' => array(
                    'company' => array(
                        'label' => 'Société',
                        'route' => 'admin/playgrounddesign_companyadmin',
                        'resource' => 'design',
                        'privilege' => 'system',
                    ),
                    'theme' => array(
                        'label' => 'Gestion des thèmes',
                        'route' => 'admin/playgrounddesign_themeadmin',
                        'resource' => 'design',
                        'privilege' => 'system',
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'XHTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_path_stack' => array(
            __DIR__ . '/../view/admin',
            __DIR__ . '/../view/frontend',
        ),
    ),
);
