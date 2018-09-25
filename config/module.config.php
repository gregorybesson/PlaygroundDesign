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
                
                array('controller' => 'PlaygroundDesign\Controller\Frontend\Home',  'roles' => array('guest', 'user')),
                array('controller' => 'PlaygroundDesign\Controller\Admin\System',   'roles' => array('admin')),
                array('controller' => 'PlaygroundDesign\Controller\Admin\Dashboard','roles' => array('admin')),
                array('controller' => 'PlaygroundDesign\Controller\Admin\Company',  'roles' => array('admin')),
                array('controller' => 'PlaygroundDesign\Controller\Admin\Theme',    'roles' => array('admin')),
            ),
        ),
    ),
    
    'doctrine' => array(
        'driver' => array(
            'playgrounddesign_entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => __DIR__ . '/../src/Entity'
            ),

            'orm_default' => array(
                'drivers' => array(
                    'PlaygroundDesign\Entity'  => 'playgrounddesign_entity'
                )
            )
        )
    ),

    'service_manager' => array(
        'aliases' => array(
            'playgrounddesign_doctrine_em' => 'doctrine.entitymanager.orm_default',
            'Zend\Mvc\View\Http\InjectTemplateListener' => \PlaygroundDesign\View\Http\InjectTemplateListener::class,
        ),
        'factories' => array(
            \PlaygroundDesign\View\Http\InjectTemplateListener::class => \PlaygroundDesign\View\Http\InjectTemplateListenerFactory::class,
            'translator' => 'Zend\Mvc\Service\TranslatorServiceFactory',
            'nav' => 'Zend\Navigation\Service\DefaultNavigationFactory',
            'admin_navigation' => 'PlaygroundDesign\Service\Factory\AdminNavigationFactory',
            'playgrounddesign_theme_service' => 'PlaygroundDesign\Service\Factory\ThemeFactory',
            'playgrounddesign_company_service' => 'PlaygroundDesign\Service\Factory\CompanyFactory'
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
            'type' => 'Zend\Router\Http\Literal',
            'priority' => 1000,
            'options' => array(
            'route'    => '/admin',
            'defaults' => array(
                'controller' => 'PlaygroundDesign\Controller\Admin\Dashboard',
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
                    'controller' => 'PlaygroundDesign\Controller\Admin\Dashboard',
                    'action'     => 'index',
                ),
                ),
            ),
            'playgrounddesign_companyadmin' => array(
                'type' => 'Zend\Router\Http\Literal',
                'options' => array(
                'route'    => '/company',
                'defaults' => array(
                    'controller' => 'PlaygroundDesign\Controller\Admin\Company',
                    'action'     => 'index',
                ),
                ),
                'may_terminate' => true,
            ),
            'playgrounddesign_themeadmin' => array(
                'type' => 'Zend\Router\Http\Literal',
                'options' => array(
                    'route'    => '/theme',
                    'defaults' => array(
                        'controller' => 'PlaygroundDesign\Controller\Admin\Theme',
                        'action'     => 'list',
                    ),
                ),
                'may_terminate' => true,
            ),
            'playgrounddesign_themeadmin_new' => array(
                'type' => 'Zend\Router\Http\Literal',
                'options' => array(
                    'route'    => '/theme/new',
                    'defaults' => array(
                        'controller' => 'PlaygroundDesign\Controller\Admin\Theme',
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
                    'controller' => 'PlaygroundDesign\Controller\Admin\Theme',
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
                    'controller' => 'PlaygroundDesign\Controller\Admin\Theme',
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
                    'controller' => 'PlaygroundDesign\Controller\Admin\Theme',
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
                    'controller' => 'PlaygroundDesign\Controller\Admin\System',
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
                        'controller' => 'PlaygroundDesign\Controller\Admin\System',
                        'action'     => 'modules',
                    ),
                    )
                ),
                'settings' => array(
                    'type' => 'literal',
                    'options' => array(
                    'route'    => '/settings',
                    'defaults' => array(
                        'controller' => 'PlaygroundDesign\Controller\Admin\System',
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
        'factories' => array(
            \PlaygroundDesign\Controller\Frontend\Home::class => \PlaygroundDesign\Service\Factory\FrontendHomeControllerFactory::class,
            \PlaygroundDesign\Controller\Admin\Dashboard::class => \PlaygroundDesign\Service\Factory\AdminDashboardControllerFactory::class,
            \PlaygroundDesign\Controller\Admin\System::class => \PlaygroundDesign\Service\Factory\AdminSystemControllerFactory::class,
            \PlaygroundDesign\Controller\Admin\Company::class => \PlaygroundDesign\Service\Factory\AdminCompanyControllerFactory::class,
            \PlaygroundDesign\Controller\Admin\Theme::class => \PlaygroundDesign\Service\Factory\AdminThemeControllerFactory::class,
        ),
    ),
    
    'controller_plugins' => array(
        // 'invokables' => array(
        //     'frontendUrl' => 'PlaygroundDesign\Controller\Plugin\FrontendUrl',
        // ),
        'factories' => array(
            'frontendUrl'    => \PlaygroundDesign\Controller\Plugin\FrontendUrlFactory::class,
        ),
    ),

    'translator' => array(
        'locale' => 'fr_FR',
        'translation_file_patterns' => array(
            array(
                'type' => 'phpArray',
                'base_dir' => __DIR__ . '/../../../language',
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
                'target' => 'nav-icon icon-settings',
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
