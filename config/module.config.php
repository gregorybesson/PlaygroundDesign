<?php
return array(

    'bjyauthorize' => array(
        'resource_providers' => array(
            'BjyAuthorize\Provider\Resource\Config' => array(
                'design'        => array(),
            ),
        ),
    
        'rule_providers' => array(
            'BjyAuthorize\Provider\Rule\Config' => array(
                'allow' => array(
                    array(array('admin'), 'design',         array('menu','system')),
                ),
            ),
        ),
    
        'guards' => array(
            'BjyAuthorize\Guard\Controller' => array(
                
                array('controller' => 'PlaygroundDesign\Controller\Frontend\Home',             'roles' => array('guest', 'user')),
                array('controller' => 'PlaygroundDesign\Controller\System',                     'roles' => array('admin')),
                array('controller' => 'PlaygroundDesign\Controller\Dashboard',                  'roles' => array('admin')),
                array('controller' => 'PlaygroundDesign\Controller\CompanyAdmin',               'roles' => array('admin')),
                array('controller' => 'PlaygroundDesign\Controller\ThemeAdmin',                 'roles' => array('admin')),
            ),
        ),
    ),
    
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

    'service_manager' => array(
        'factories' => array(
            // this definition has to be done here to override Wilmogrod Assetic declaration
            'AsseticBundle\Service' => 'PlaygroundDesign\Assetic\ServiceFactory',
            'translator' => 'Zend\Mvc\Service\TranslatorServiceFactory',
            'nav' => 'Zend\Navigation\Service\DefaultNavigationFactory',
        ),
        'invokables' => array(
            // this definition has to be done here to override Wilmogrod Assetic declaration
            'AsseticBundle\Listener' => 'PlaygroundDesign\Assetic\Listener',
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
            'lib' => array(
                # module root path for your css and js files
                'root_path' => array(
                    __DIR__ . '/../view/lib',
                ),
                # collection of assets
                'collections' => array(
                    'jquery' => array(
                        'assets' => array(
                            'js/jquery.min.js',
                        ),
                    ),
                    'jquery_ui' => array(
                        'assets' => array(
                            'js/jquery-ui.min.js',
                        ),
                    ),
                    'jquery_validate' => array(
                        'assets' => array(
                            'js/jquery.validate.min.js',
                        ),
                    ),
                    'bootstrap' => array(
                        'assets' => array(
                            'js/bootstrap.min.js',
                        ),
                    ),
                    'bootstrap_switch' => array(
                        'assets' => array(
                            'js/bootstrap-switch.min.js',
                        ),
                    ),
                    'jquery_ui_timepicker_addon' => array(
                        'assets' => array(
                            'js/jquery-ui-timepicker-addon.js',
                        ),
                    ),
                    'json' => array(
                        'assets' => array(
                           'js/json.js',
                        ),
                    ),
                    'bowser' => array(
                        'assets' => array(
                            'js/bowser.min.js',
                        ),
                    ),
                    'jscrollpane' => array(
                        'assets' => array(
                            'js/jscrollpane.js',
                        ),
                    ),
                    'mousewheel' => array(
                        'assets' => array(
                            'js/mousewheel.js',
                        ),
                    ),
                    'jquery_nivo_slider' => array(
                        'assets' => array(
                            'js/jquery.nivo.slider.js',
                        ),
                    ),
                    'jquery_uniform' => array(
                        'assets' => array(
                            'js/jquery.uniform-2.0.js',
                        ),
                    ),
                    'jquery_limit' => array(
                        'assets' => array(
                            'js/jquery.limit-1.2.source.js',
                        ),
                    ),
                    'wscratchpad' => array(
                        'assets' => array(
                            'js/wScratchPad.js',
                        ),
                    ),
                    'jquery_timer' => array(
                        'assets' => array(
                            'js/jquery.timer.js',
                        ),
                    ),

                    'bootstrap_css' => array(
                        'assets' => array(
                            'css/bootstrap.min.css',
                        ),
                    ),
                    'bootstrap_switch_css' => array(
                        'assets' => array(
                            'css/bootstrap-switch.css',
                        ),
                    ),
                    'bootstrap_responsive_css' => array(
                        'assets' => array(
                            'css/bootstrap-responsive.min.css',
                        ),
                    ),
                    'jquery_ui_css' => array(
                        'assets' => array(
                            'http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css',
                        ),
                    ),
                    'datepicker_css' => array(
                        'assets' => array(
                            'css/datepicker.css',
                        ),
                    ),
                    'jquery_ui_timepicker_addon_css' => array(
                        'assets' => array(
                            'css/jquery-ui-timepicker-addon.css',
                        ),
                    ),
                    'uniform_default_css' => array(
                        'assets' => array(
                            'css/uniform.default.css',
                        ),
                    ),

                    'admin_lib_css' => array(
                        'assets' => array(
                            '@bootstrap_css',
                            '@bootstrap_switch_css',
                            '@bootstrap_responsive_css',
                            '@jquery_ui_css',
                            '@datepicker_css',
                            '@jquery_ui_timepicker_addon_css',
                        ),
                        'filters' => array(),
                        'options' => array(
                            'output' => 'zfcadmin/css/head_lib.css'
                        ),
                    ),
                    'head_admin_lib_js' => array(
                        'assets' => array(
                            '@jquery',
                            '@jquery_ui',
                            '@jquery_validate',
                            '@bootstrap',
                            '@bootstrap_switch',
                            '@jquery_ui_timepicker_addon',
                            '@json',
                        ),
                        'filters' => array(),
                        'options' => array(
                            'output' => 'zfcadmin/js/head_lib.js',
                        ),
                    ),

                    'frontend_lib_css' => array(
                        'assets' => array(
                            '@uniform_default_css',
                        ),
                        'filters' => array(),
                        'options' => array(
                            'output' => 'frontend/css/lib.css'
                        ),
                    ),

                    'head_frontend_lib_js' => array(
                        'assets' => array(
                            '@jquery',
                            '@jquery_ui',
                            '@bowser',
                            '@jscrollpane',
                            '@mousewheel',
                            '@jquery_nivo_slider',
                            '@jquery_uniform',
                            '@jquery_limit',
                            '@wscratchpad',
                            '@jquery_timer',
                            '@jquery_validate',
                            '@bootstrap',
                            '@bootstrap_switch',
                        ),
                        'filters' => array(),
                        'options' => array(
                            'output' => 'frontend/js/head_lib.js',
                        ),
                    ),

                    'ckeditor' => array(
                        'assets' => array(
                            'js/ckeditor/*',
                            'js/ckeditor/**/*',
                            'js/ckeditor/**/**/*',
                            'js/ckeditor/**/**/**/*',
                            'js/ckeditor-custom/*',
                            'js/ckeditor-custom/**/*',
                            'css/ckeditor-custom/*',
                        ),
                        'options' => array(
                            'move_raw' => true,
                            'output' => 'lib',
                        )
                    ),

                    'admin_elfinder' => array(
                        'assets' => array(
                            'js/elfinder/*',
                            'js/elfinder/**/*',
                            'js/elfinder/**/**/*',
                            'js/elfinder/**/**/**/*',
                            'js/elfinder/**/**/**/**/*',
                            'js/elfinder/**/**/**/**/**/*',
                        ),
                        'options' => array(
                            'move_raw' => true,
                            'output' => 'lib',
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
                            'output' => 'lib',
                        )
                    ),
                    'admin_jquery_min_map' => array(
                        'assets' => array(
                            'js/jquery.min.map',
                        ),
                        'options' => array(
                            'move_raw' => true,
                            'output' => 'lib',
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
                            'output' => 'zfcadmin'
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
            'admin' => array(
                # module root path for your css and js files
                'root_path' => array(
                        __DIR__ . '/../view/admin/assets',
                ),
                # collection of assets
                'collections' => array(
                    'admin_css' => array(
                        'assets' => array(
                            'ie8.css'                        => 'css/ie8.css',
                            'ie.css'                         => 'css/ie.css',
                            'administration.css'             => 'css/administration.css',
                        ),
                        'filters' => array(),
                        'options' => array(
                            'output' => 'zfcadmin/css/main'
                        ),
                    ),
                    'head_admin_js' => array(
                        'assets' => array(
                            'admin.js' => 'js/admin.js',
                            'drag.js'  => 'js/drag.js',
                        ),
                        'filters' => array(),
                        'options' => array(
                            'output' => 'zfcadmin/js/head_main.js',
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
                        ),
                        'filters' => array(),
                        'options' => array(
                            'output' => 'frontend/css/main'
                        ),
                    ),
                    'head_frontend_js' => array(
                        'assets' => array(
                            'loader.js'     => 'js/loader.js',
                            'popin.js'      => 'js/popin.js',
                            'functions.js'  => 'js/functions.js',
                            'script.js'     => 'js/script.js',
                            'users.js'      => 'js/users.js',
                            'share.js'      => 'js/share.js',
                            'games.js'      => 'js/games.js',
                        ),
                        'filters' => array(),
                        'options' => array(
                            'output' => 'frontend/js/head_main',
                        ),
                    ),
//                     'frontend_images' => array(
//                         'assets' => array(
//                             'images/**/*.png',
//                             'images/**/*.jpg',
//                         ),
//                         'options' => array(
//                             'move_raw' => true,
//                             'output' => 'frontend',
//                         )
//                     ),
                ),
            ),
        ),

        'routes' => array(
            'admin.*' => array(
                '@admin_lib_css'     => '@admin_lib_css',
                '@admin_css'         => '@admin_css',
                '@head_admin_lib_js' => '@head_admin_lib_js',
                '@head_admin_js'     => '@head_admin_js',
            ),
            'frontend.*' => array(
                '@frontend_lib_css'     => '@frontend_lib_css',
                '@frontend_css'         => '@frontend_css',
                '@head_frontend_lib_js' => '@head_frontend_lib_js',
                '@head_frontend_js'     => '@head_frontend_js',
            ),
            'error_404' => array(
                '@frontend_lib_css'     => '@frontend_lib_css',
                '@frontend_css'         => '@frontend_base_css',
                '@head_frontend_lib'    => '@head_frontend_base_lib',
                '@head_frontend_js'     => '@head_frontend_base_js',
            ),
        ),
    ),

  'router' => array(
    'routes' => array(
      'frontend' => array(
        'options' => array(
          'defaults' => array(
            'controller' => 'PlaygroundDesign\Controller\Frontend\Home',
            'action'     => 'index',
          ),
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
            'PlaygroundDesign\Controller\Frontend\Home' => 'PlaygroundDesign\Controller\Frontend\HomeController',
            'PlaygroundDesign\Controller\Dashboard' => 'PlaygroundDesign\Controller\DashboardController',
            'PlaygroundDesign\Controller\System'    => 'PlaygroundDesign\Controller\SystemController',
            'PlaygroundDesign\Controller\CompanyAdmin' => 'PlaygroundDesign\Controller\CompanyAdminController',
            'PlaygroundDesign\Controller\themeAdmin' => 'PlaygroundDesign\Controller\ThemeAdminController',
        ),
    ),
    
    'controller_plugins' => array(
        'invokables' => array(
            'frontendUrl' => 'PlaygroundDesign\Controller\Plugin\FrontendUrl',
        ),
    ),

    'translator' => array(
        'locale' => 'fr_FR',
        'translation_file_patterns' => array(
            array(
                'type' => 'phpArray',
                'base_dir' => __DIR__ . '/../../../../language',
                'pattern' => '%s.php',
                'text_domain' => 'playgrounddesign'
            ),
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
                'privilege' => 'menu',
                'pages' => array(
                    'company' => array(
                        'label' => 'Company',
                        'route' => 'admin/playgrounddesign_companyadmin',
                        'resource' => 'design',
                        'privilege' => 'system',
                    ),
                    'theme' => array(
                        'label' => 'Themes',
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
