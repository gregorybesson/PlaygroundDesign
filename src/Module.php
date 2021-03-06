<?php

namespace PlaygroundDesign;

use Laminas\Session\SessionManager;
use Laminas\Session\Config\SessionConfig;
use Laminas\Session\Container;
use Laminas\Validator\AbstractValidator;
use Laminas\ModuleManager\ModuleManager;
use Laminas\ModuleManager\ModuleEvent;
use Laminas\EventManager\EventInterface;
use Laminas\ModuleManager\Feature\AutoloaderProviderInterface;
use Laminas\ModuleManager\Feature\BootstrapListenerInterface;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\ModuleManager\Feature\ServiceProviderInterface;
use Laminas\ModuleManager\Feature\ViewHelperProviderInterface;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Adapter\Adapter;

class Module implements
    BootstrapListenerInterface,
    ConfigProviderInterface,
    ServiceProviderInterface,
    ViewHelperProviderInterface
{

    public $themeMapper;
    public $serviceManager = null;

    public function init(ModuleManager $manager)
    {
        $eventManager = $manager->getEventManager();

        /*
         * This event change the config before it's cached
         * The change will apply to 'template_path_stack'
         * This config take part in the Playground Theme Management
         */
        $eventManager->attach(\Laminas\ModuleManager\ModuleEvent::EVENT_MERGE_CONFIG, array($this, 'onMergeConfig'), 100);
    }

    /**
     * This method is called only when the config is not cached.
     * @param Event $e
     */
    public function onMergeConfig($e)
    {
        $stack = array();
        $config = $e->getConfigListener()->getMergedConfig(false);

        $configDatabaseDoctrine = $config['doctrine']['connection']['orm_default']['params'];
        $configDatabase = array('driver' => 'Mysqli',
            'database' => $configDatabaseDoctrine['dbname'],
            'username' => $configDatabaseDoctrine['user'],
            'password' => $configDatabaseDoctrine['password'],
            'hostname' => $configDatabaseDoctrine['host']);

        if (!empty($configDatabaseDoctrine['port'])) {
            $configDatabase['port'] = $configDatabaseDoctrine['port'];
        }
        if (!empty($configDatabaseDoctrine['charset'])) {
            $configDatabase['charset'] = $configDatabaseDoctrine['charset'];
        }

        if (PHP_SAPI !== 'cli') {
            $adapter = new Adapter($configDatabase);
            $sql = new Sql($adapter);
    
            // ******************************************
            // Check if an admin theme is set in database
            // ******************************************
            $select = $sql->select();
            $select->from('design_theme');
            $select->where(array('is_active' => 1, 'area' => 'admin'));
            $select->limit(1);
            $statement = $sql->prepareStatementForSqlObject($select);
            $results = $statement->execute();
            foreach ($results as $result) {
                $themeActivated = $result;
            }
            
            if (!empty($themeActivated)) {
                $config['design']['admin']['package'] = $themeActivated['package'];
                $config['design']['admin']['theme'] = $themeActivated['theme'];
            }
            
            // ********************************************
            // Check if a frontend theme is set in database
            // ********************************************
            $themeActivated = null;
            $select = $sql->select();
            $select->from('design_theme');
            $select->where(array('is_active' => 1, 'area' => 'frontend'));
            $select->limit(1);
            $statement = $sql->prepareStatementForSqlObject($select);
            $results = $statement->execute();
            foreach ($results as $result) {
                $themeActivated = $result;
            }
            
            // Surchage par le theme qui est activé en base de donnée
            if (!empty($themeActivated)) {
                $config['design']['frontend']['package'] = $themeActivated['package'];
                $config['design']['frontend']['theme'] = $themeActivated['theme'];
            }
        }
        
        // **************************************************
        // Design management : template and assets management
        // **************************************************
        if (isset($config['design'])) {
            $viewResolverPathStack = $config['view_manager']['template_path_stack'];
            
            if (isset($config['design']['admin']['theme'])) {
                $themeHierarchy = array();
                $hasParent = true;
                $parentTheme = array($config['design']['admin']['package'], $config['design']['admin']['theme']);
        
                while ($hasParent) {
                    // I get the Theme definition file and apply a check on the parent theme.
                    // TODO : Apply recursion to this stuff.
                    $hasParent = false;
                    $adminPath = __DIR__ . '/../../../../design/admin/'. $parentTheme[0] .'/'. $parentTheme[1];
                    $themeId = $parentTheme[0] .'_'. $parentTheme[1];

                    if (!(strtolower($themeId) === 'playground_base')) {
                        $adminThemePath = $adminPath . '/theme.php';
        
                        if (is_file($adminThemePath) && is_readable($adminThemePath)) {
                            $configTheme                      = new \Laminas\Config\Config(include $adminThemePath);
                            $themeHierarchy[$themeId]['path'] = $adminPath;
        
                            $pathStack = array($adminPath);
                            $themeHierarchy[$themeId]['template_path']= $pathStack;
        
                            $assets = $adminPath . '/assets.php';
                            if (is_file($assets) && is_readable($assets)) {
                                $configAssets = new \Laminas\Config\Config(include $assets);
                                $themeHierarchy[$themeId]['assets'] = $configAssets->toArray();
                            }
        
                            $layout = $adminPath . '/layout.php';
                            if (is_file($layout) && is_readable($layout)) {
                                $configLayout = new \Laminas\Config\Config(include $layout);
                                $themeHierarchy[$themeId]['layout'] = $configLayout->toArray();
                            }
        
                            if (isset($configTheme['design']['package']['theme']['parent'])) {
                                $parentTheme = explode('_', $configTheme['design']['package']['theme']['parent']);
                                $parentThemeId = $parentTheme[0] .'_'. $parentTheme[1];
        
                                if ($parentThemeId  == $themeId) {
                                    $hasParent = false;
                                }
        
                                $hasParent = true;
                            }
                        }
                    } else {
                        $stack = array();
                        foreach ($viewResolverPathStack as $path) {
                            if ($result = preg_match('/\/admin\/$/', $path, $matches)) {
                                $stack[] = $path;
                            }
                        }
                        $themeHierarchy[$themeId]['template_path']= array_reverse($stack);
        
                        if (isset($config['core_layout']['admin'])) {
                            $themeHierarchy[$themeId]['layout'] = $config['core_layout']['admin'];
                        }
                    }
                }
        
                // I reverse the array of $themeHierarchy to have Parents -> children
                $themeHierarchy = array_reverse($themeHierarchy);
        
                // We remove the former config
                $stack = array();
                foreach ($viewResolverPathStack as $path) {
                    if (!$result = preg_match('/\/admin\/$/', $path, $matches)) {
                        $stack[] = $path;
                    }
                }

                $viewResolverPathStack = array_reverse($stack);
        
                // removing default layout configuration
                if (isset($config['core_layout']['admin'])) {
                    unset($config['core_layout']['admin']);
                }
                
                //clearing the template_path_stack
                $config['view_manager']['template_path_stack'] = $viewResolverPathStack;
        
                //I then recreate the config
                foreach ($themeHierarchy as $theme => $tab) {
                    if (isset($tab['layout'])) {
                        $config = array_replace_recursive($config, $tab['layout']);
                    }
        
                    if (isset($tab['assets'])) {
                        $config = array_replace_recursive($config, $tab['assets']);
                    }
        
                    if (isset($tab['template_path'])) {
                        $config['view_manager']['template_path_stack'] = array_merge($config['view_manager']['template_path_stack'], $tab['template_path']);
                    }
                }
            }
        
            if (isset($config['design']['frontend']['theme'])) {
                $viewResolverPathStack = array_reverse($config['view_manager']['template_path_stack']);
                $themeHierarchy = array();
                $hasParent = true;
                $parentTheme = array($config['design']['frontend']['package'], $config['design']['frontend']['theme']);
        
                while ($hasParent) {
                    // I get the Theme definition file and apply a check on the parent theme.
                    // TODO : Apply recursion to this stuff.
                    $hasParent = false;
                    $frontendPath = __DIR__ . '/../../../../design/frontend/'. $parentTheme[0] .'/'. $parentTheme[1];
                    $themeId = $parentTheme[0] .'_'. $parentTheme[1];
        
                    if (!(strtolower($themeId) === 'playground_base')) {
                        $frontendThemePath = $frontendPath . '/theme.php';
        
                        if (is_file($frontendThemePath) && is_readable($frontendThemePath)) {
                            $configTheme                      = new \Laminas\Config\Config(include $frontendThemePath);
                            $themeHierarchy[$themeId]['path'] = $frontendPath;
        
                            $pathStack = array($frontendPath);
                            $themeHierarchy[$themeId]['template_path']= $pathStack;
        
                            $assets = $frontendPath . '/assets.php';
                            if (is_file($assets) && is_readable($assets)) {
                                $configAssets = new \Laminas\Config\Config(include $assets);
                                $themeHierarchy[$themeId]['assets'] = $configAssets->toArray();
                            }
        
                            $layout = $frontendPath . '/layout.php';
                            if (is_file($layout) && is_readable($layout)) {
                                $configLayout = new \Laminas\Config\Config(include $layout);
                                $themeHierarchy[$themeId]['layout'] = $configLayout->toArray();
                            }
        
                            if (isset($configTheme['design']['package']['theme']['parent'])) {
                                $parentTheme = explode('_', $configTheme['design']['package']['theme']['parent']);
                                $parentThemeId = $parentTheme[0] .'_'. $parentTheme[1];
        
                                if ($parentThemeId  == $themeId) {
                                    $hasParent = false;
                                } else {
                                    $hasParent = true;
                                }
                            }
                        }
                    } else {
                        $stack = array();
                        foreach ($viewResolverPathStack as $path) {
                            if ($result = preg_match('/\/frontend\/$/', $path, $matches)) {
                                $stack[] = $path;
                            }
                        }
        
                        $themeHierarchy[$themeId]['template_path']= array_reverse($stack);
        
                        if (isset($config['core_layout']['frontend'])) {
                            $themeHierarchy[$themeId]['layout'] = $config['core_layout']['frontend'];
                        }
                    }
                }
        
                $themeHierarchy = array_reverse($themeHierarchy);
                
                // We remove the former config
                $stack = array();
                foreach ($viewResolverPathStack as $path) {
                    if (!$result = preg_match('/\/frontend\/$/', $path, $matches)) {
                        $stack[] = $path;
                    }
                }
        
                $viewResolverPathStack = array();
        
                $viewResolverPathStack = array_reverse($stack);
        
                // removing default layout configuration
                if (isset($config['core_layout']['frontend'])) {
                    unset($config['core_layout']['frontend']);
                }
        
                //clearing the template_path_stack
                $config['view_manager']['template_path_stack'] = $viewResolverPathStack;
                
                //I then recreate the config
                foreach ($themeHierarchy as $theme => $tab) {
                    if (isset($tab['layout'])) {
                        $config = array_replace_recursive($config, $tab['layout']);
                    }
        
                    if (isset($tab['assets'])) {
                        $config = array_replace_recursive($config, $tab['assets']);
                    }
        
                    if (isset($tab['template_path'])) {
                        //print_r($tab['template_path']);
                        $config['view_manager']['template_path_stack'] = array_merge($config['view_manager']['template_path_stack'], $tab['template_path']);
                    }
                }
            }
        }

        $e->getConfigListener()->setMergedConfig($config);
    }

    public function onBootstrap(EventInterface $e)
    {
        $serviceManager = $e->getApplication()->getServiceManager();
        $this->setServiceManager($serviceManager);

        $options = $serviceManager->get('playgroundcore_module_options');
        $translator = $serviceManager->get('MvcTranslator');
        $locale = $options->getLocale();
        if (!empty($locale)) {
            //translator
            $translator->setLocale($locale);

            // plugins
            $translate = $serviceManager->get('ViewHelperManager')->get('translate');
            $translate->getTranslator()->setLocale($locale);
        }

        AbstractValidator::setDefaultTranslator($translator, 'playgrounddesign');
        
        // Start the session container
        $config = $e->getApplication()->getServiceManager()->get('config');
        
        /**
         * This listener gives the possibility to select the layout on module / controller / action level !
         * Just configure it in any module config or autoloaded config.
         */
        $e->getApplication()->getEventManager()->getSharedManager()->attach(\Laminas\Mvc\Controller\AbstractActionController::class, 'dispatch', function ($e) {
            $config     = $e->getApplication()->getServiceManager()->get('config');
            if (isset($config['core_layout'])) {
                $controller      = $e->getTarget();
                $controllerClass = get_class($controller);
                $moduleName      = strtolower(substr($controllerClass, 0, strpos($controllerClass, '\\')));
                $match           = $e->getRouteMatch();
                $routeName       = $match->getMatchedRouteName();
                $areaName        = (strpos($routeName, '/'))?substr($routeName, 0, strpos($routeName, '/')):$routeName;
                $areaName        = ($areaName == 'frontend' || $areaName == 'admin')? $areaName : 'frontend';
                $controllerName  = $match->getParam('controller', 'not-found');
                $actionName      = $match->getParam('action', 'not-found');
                $viewModel       = $e->getViewModel();
                
                // I add this area param so that it can be used by Controller plugin frontendUrl
                // and View helper frontendUrl
                $match->setParam('area', $areaName);

                // echo '$controllerClass : ' . $controllerClass . '<br/>';
                // echo '$moduleName : ' .$moduleName. '<br/>';
                // echo '$routeName : '.$routeName. '<br/>';
                // echo '$areaName : '.$areaName. '<br/>';
                // echo '$controllerName : ' .$controllerName. '<br/>';
                // echo '$actionName : ' . $actionName. '<br/>';

                /**
                 * Assign the correct layout
                 */

                if (isset($config['core_layout'][$areaName]['modules'][$moduleName]['controllers'][$controllerName]['actions'][$actionName]['layout'])) {
                    //print_r($config['core_layout'][$areaName]['modules'][$moduleName]['controllers'][$controllerName]['actions'][$actionName]['layout']);
                    $controller->layout($config['core_layout'][$areaName]['modules'][$moduleName]['controllers'][$controllerName]['actions'][$actionName]['layout']);
                } elseif (isset($config['core_layout'][$areaName]['modules'][$moduleName]['controllers'][$controllerName]['layout'])) {
                    //print_r($config['core_layout'][$areaName]['modules'][$moduleName]['controllers'][$controllerName]['layout']);
                    $controller->layout($config['core_layout'][$areaName]['modules'][$moduleName]['controllers'][$controllerName]['layout']);
                } elseif (isset($config['core_layout'][$areaName]['modules'][$moduleName]['layout'])) {
                    //print_r($config['core_layout'][$areaName]['modules'][$moduleName]['layout']);
                    $controller->layout($config['core_layout'][$areaName]['modules'][$moduleName]['layout']);
                } elseif (isset($config['core_layout'][$areaName]['layout'])) {
                    $controller->layout($config['core_layout'][$areaName]['layout']);
                }

                /**
                 * Create variables attached to layout containing path views
                 * cascading assignment is managed
                 */
                if (isset($config['core_layout'][$areaName]['modules'][$moduleName]['children_views'])) {
                    foreach ($config['core_layout'][$areaName]['modules'][$moduleName]['children_views'] as $k => $v) {
                        $viewModel->$k  = $v;
                    }
                }
                if (isset($config['core_layout'][$areaName]['modules'][$moduleName]['controllers'][$controllerName]['children_views'])) {
                    foreach ($config['core_layout'][$areaName]['modules'][$moduleName]['controllers'][$controllerName]['children_views'] as $k => $v) {
                        $viewModel->$k  = $v;
                    }
                }
                if (isset($config['core_layout'][$areaName]['modules'][$moduleName]['controllers'][$controllerName]['actions'][$actionName]['children_views'])) {
                    foreach ($config['core_layout'][$areaName]['modules'][$moduleName]['controllers'][$controllerName]['actions'][$actionName]['children_views'] as $k => $v) {
                        $viewModel->$k  = $v;
                    }
                }
            }
        }, 100);
        
        // I put area to each view
        $e->getApplication()->getEventManager()->attach(\Laminas\Mvc\MvcEvent::EVENT_RENDER, function (\Laminas\Mvc\MvcEvent $e) use ($serviceManager) {
        
            $viewModel = $e->getViewModel();
            $match = $e->getRouteMatch();
            $area = isset($match)? $match->getParam('area', ''):'';
            $sm = $e->getApplication()->getServiceManager();
            if ($match) {
                $title = $match->getParam('title');
                $action = $match->getParam('action');
                $controller = explode('\\', $match->getParam('controller'));
                $controller = end($controller);
                $headTitleHelper = $sm->get('ViewHelperManager')->get('headTitle');

                if (empty($title)) {
                    $title = $controller . '-' . $action;
                }
                $title = $sm->get('MvcTranslator')->translate($title, 'routes');

                if ($title !== ' ' && !empty($title)) {
                    $headTitleHelper->prepend($title);
                }
            }

            $viewModel->area    = $area;
            
            foreach ($viewModel->getChildren() as $child) {
                $child->area = $area;
            }
        }, -1000);
    }

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getViewHelperConfig()
    {
        return array(
            'aliases' => array(
                'routeMatch' => \PlaygroundDesign\View\Helper\RouteMatchWidget::class,
            ),
            'factories' => array(
                
                \PlaygroundDesign\View\Helper\RouteMatchWidget::class => \PlaygroundDesign\View\Helper\RouteMatchWidgetFactory::class,
                'frontendUrl' => function ($sm) {
                    $view_helper =  new View\Helper\FrontendUrl();
                    $view_helper->setRouter($sm->get('HttpRouter'));
                
                    $match = $sm->get('Application')->getMvcEvent()->getRouteMatch();
                
                    if ($match instanceof \Laminas\Router\Http\RouteMatch) {
                        $view_helper->setRouteMatch($match);
                    }
                
                    return $view_helper;
                },
                'adminUrl' => function ($sm) {
                    $view_helper =  new View\Helper\AdminUrl();
                    $view_helper->setRouter($sm->get('HttpRouter'));
                
                    $match = $sm->get('Application')->getMvcEvent()->getRouteMatch();
                
                    if ($match instanceof \Laminas\Router\Http\RouteMatch) {
                        $view_helper->setRouteMatch($match);
                    }
                
                    return $view_helper;
                },

                // This admin navigation layer gives the authentication layer based on BjyAuthorize ;)
                'adminMenu' => function ($sm) {
                    $helperPluginManager = $sm->get('ViewHelperManager');
                    $nav = $helperPluginManager->get('navigation')->menu('admin_navigation');
                    $nav->setUlClass('nav')
                        ->setMaxDepth(10)
                        ->setRenderInvisible(false);

                    return $nav;
                },

                'adminAssetPath' => function ($sm) {
                    $config = $sm->has('Config') ? $sm->get('Config') : array();
                    $helper  = new View\Helper\AdminAssetPath;
                    if (isset($config['view_manager']) && isset($config['view_manager']['base_path'])) {
                        $basePath = $config['view_manager']['base_path'];
                    } else {
                        $basePath = $sm->get('Request')->getBasePath();
                    }
                    $helper->setBasePath($basePath);
                    return $helper;
                },

                'frontendAssetPath' => function ($sm) {
                    $config = $sm->has('Config') ? $sm->get('Config') : array();
                    $helper  = new View\Helper\FrontendAssetPath;
                    if (isset($config['view_manager']) && isset($config['view_manager']['base_path'])) {
                        $basePath = $config['view_manager']['base_path'];
                    } else {
                        $basePath = $sm->get('Request')->getBasePath();
                    }
                    $helper->setBasePath($basePath);
                    return $helper;
                },

                'libAssetPath' => function ($sm) {
                    $config = $sm->has('Config') ? $sm->get('Config') : array();
                    $helper  = new View\Helper\LibAssetPath;
                    if (isset($config['view_manager']) && isset($config['view_manager']['base_path'])) {
                        $basePath = $config['view_manager']['base_path'];
                    } else {
                        $basePath = $sm->get('Request')->getBasePath();
                    }
                    $helper->setBasePath($basePath);
                    return $helper;
                },

                'facebookUrl' => function ($sm) {
                    $fbUrl = null;
                    $config = $sm->get('config');
                    if (isset($config['facebook_url'])) {
                        $fbUrl = $config['facebook_url'];
                    }
                    $viewHelper = new View\Helper\FacebookUrl($fbUrl);

                    return $viewHelper;
                },

                'head' => function ($sm) {
                    return new View\Helper\Head();
                },
                'header' => function ($sm) {
                    return new View\Helper\Header();
                },
                'column_right' => function ($sm) {
                    $rssUrl = '';
                    $config = $sm->get('config');
                    if (isset($config['rss']['url'])) {
                        $rssUrl = $config['rss']['url'];
                    }
                    $viewHelper = new View\Helper\ColumnRight();
                    $viewHelper->setRssUrl($rssUrl);

                    return $viewHelper;
                },
                'column_left' => function ($sm) {
                    return new View\Helper\ColumnLeft();
                },
                'footer' => function ($sm) {
                    return new View\Helper\Footer();
                },
                'companyWidget' => function ($sm) {
                    return new View\Helper\CompanyWidget($sm->get('playgrounddesign_company_mapper'));
                },
                'debugView' => function ($sm) {
                    return new View\Helper\DebugView();
                }
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'playgrounddesign_module_options' => function ($sm) {
                    $config = $sm->get('Configuration');

                    return new Options\ModuleOptions(isset($config['playgrounddesign']) ? $config['playgrounddesign'] : array());
                },
                'playgrounddesign_theme_mapper' => function ($sm) {

                    return new Mapper\Theme($sm->get('playgrounddesign_doctrine_em'), $sm->get('playgrounddesign_module_options'));
                },

                'playgrounddesign_company_mapper' => function ($sm) {
                    return new Mapper\Company($sm->get('playgrounddesign_doctrine_em'), $sm->get('playgrounddesign_module_options'));
                },
                'playgrounddesign_settings_mapper' => function ($sm) {
                    return new Mapper\Settings($sm->get('playgrounddesign_doctrine_em'), $sm->get('playgrounddesign_module_options'));
                },
                'playgrounddesign_theme_form' => function ($sm) {
                    $translator = $sm->get('MvcTranslator');
                    $form = new Form\Admin\Theme(null, $sm, $translator);
                    $theme = new Entity\Theme();
                    $form->setInputFilter($theme->getInputFilter());

                    return $form;
                },
                'playgrounddesign_company_form' => function ($sm) {
                    $translator = $sm->get('MvcTranslator');
                    $form = new Form\Admin\Company(null, $sm, $translator);
                    $company = new Entity\Company();
                    $form->setInputFilter($company->getInputFilter());
                    return $form;
                },
                'playgrounddesign_settings_form' => function ($sm) {
                    $translator = $sm->get('MvcTranslator');
                    $form = new Form\Admin\Settings(null, $sm, $translator);
                    $settings = new Entity\Settings();
                    $form->setInputFilter($settings->getInputFilter());

                    return $form;
                },
            ),
        );
    }

    /**
    * Recuperation du themeMapper
    *
    * @return PlaygroundDesign\Mapper\Theme $themeMapper
    */
    public function getThemeMapper($sm)
    {
        if (null === $this->themeMapper) {
            $this->themeMapper = $sm->get('playgrounddesign_theme_mapper');
        }

        return $this->themeMapper;
    }

    public function setServiceManager($sm)
    {
        $this->serviceManager = $sm;

        return $this;
    }


    public function getServiceManager()
    {
        return $this->serviceManager;
    }
}
