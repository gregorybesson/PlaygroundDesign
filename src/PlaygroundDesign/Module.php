<?php

namespace PlaygroundDesign;

use Zend\Session\SessionManager;
use Zend\Session\Config\SessionConfig;
use Zend\Session\Container;
use Zend\Validator\AbstractValidator;
use Zend\ModuleManager\ModuleManager;
use Zend\ModuleManager\ModuleEvent;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Adapter;

class Module implements
    AutoloaderProviderInterface,
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
         * The change will apply to 'template_path_stack' and 'assetic_configuration'
         * These 2 config take part in the Playground Theme Management
         */
        $eventManager->attach(\Zend\ModuleManager\ModuleEvent::EVENT_MERGE_CONFIG, array($this, 'onMergeConfig'), 100);
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
                    $adminPath = __DIR__ . '/../../../../../design/admin/'. $parentTheme[0] .'/'. $parentTheme[1];
                    $themeId = $parentTheme[0] .'_'. $parentTheme[1];

                    if (!(strtolower($themeId) === 'playground_base')) {
                        $adminThemePath = $adminPath . '/theme.php';
        
                        if (is_file($adminThemePath) && is_readable($adminThemePath)) {
                            $configTheme                      = new \Zend\Config\Config(include $adminThemePath);
                            $themeHierarchy[$themeId]['path'] = $adminPath;
        
                            $pathStack = array($adminPath);
                            $themeHierarchy[$themeId]['template_path']= $pathStack;
        
                            $assets = $adminPath . '/assets.php';
                            if (is_file($assets) && is_readable($assets)) {
                                $configAssets = new \Zend\Config\Config(include $assets);
                                $themeHierarchy[$themeId]['assets'] = $configAssets->toArray();
                            }
        
                            $layout = $adminPath . '/layout.php';
                            if (is_file($layout) && is_readable($layout)) {
                                $configLayout = new \Zend\Config\Config(include $layout);
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
        
        
                        if (isset($config['assetic_configuration']['modules']['admin'])) {
                            $asseticConfig = array('assetic_configuration' => array(
                                'modules' => array('admin' => $config['assetic_configuration']['modules']['admin']),
                                'routes' => array('admin.*' => $config['assetic_configuration']['routes']['admin.*'])
                            ));
        
                            $themeHierarchy[$themeId]['assets'] = $asseticConfig;
                        }
        
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
                
                // removing default assetic configuration
                if (isset($config['assetic_configuration']['modules']['admin'])) {
                    unset($config['assetic_configuration']['modules']['admin']);
                    unset($config['assetic_configuration']['routes']['admin.*']);
                }
        
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
        
                    if (isset($tab['path']) && isset($config['assetic_configuration']['modules']['admin'])) {
                        $config['assetic_configuration']['modules']['admin']['root_path'][] = $tab['path'] . '/assets';
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
                    $frontendPath = __DIR__ . '/../../../../../design/frontend/'. $parentTheme[0] .'/'. $parentTheme[1];
                    $themeId = $parentTheme[0] .'_'. $parentTheme[1];
        
                    if (!(strtolower($themeId) === 'playground_base')) {
                        $frontendThemePath = $frontendPath . '/theme.php';
        
                        if (is_file($frontendThemePath) && is_readable($frontendThemePath)) {
                            $configTheme                      = new \Zend\Config\Config(include $frontendThemePath);
                            $themeHierarchy[$themeId]['path'] = $frontendPath;
        
                            $pathStack = array($frontendPath);
                            $themeHierarchy[$themeId]['template_path']= $pathStack;
        
                            $assets = $frontendPath . '/assets.php';
                            if (is_file($assets) && is_readable($assets)) {
                                $configAssets = new \Zend\Config\Config(include $assets);
                                $themeHierarchy[$themeId]['assets'] = $configAssets->toArray();
                            }
        
                            $layout = $frontendPath . '/layout.php';
                            if (is_file($layout) && is_readable($layout)) {
                                $configLayout = new \Zend\Config\Config(include $layout);
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

                        if (isset($config['assetic_configuration']['modules']['frontend'])) {
                            $asseticConfig = array('assetic_configuration' => array(
                                'modules' => array('frontend' => $config['assetic_configuration']['modules']['frontend']),
                                'routes' => array('frontend.*' => $config['assetic_configuration']['routes']['frontend.*'])
                            ));
        
                            $themeHierarchy[$themeId]['assets'] = $asseticConfig;
                        }
        
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
        
                // removing default assetic configuration
                if (isset($config['assetic_configuration']['modules']['frontend'])) {
                    unset($config['assetic_configuration']['modules']['frontend']);
                    unset($config['assetic_configuration']['routes']['frontend.*']);
                }
        
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
        
                    if (isset($tab['path']) && isset($config['assetic_configuration']['modules']['frontend'])) {
                        $config['assetic_configuration']['modules']['frontend']['root_path'][] = $tab['path'] . '/assets';
                    }
                }
            }

            // Creating the Assetic configuration for images of all available themes
            /*if (PHP_SAPI !== 'cli') {
                $themeMapper = $this->getThemeMapper($serviceManager);
                $themes = $themeMapper->findAll();
                foreach ($themes as $theme) {
                    $moduleName = 'preview_' . $theme->getArea() . '_' . $theme->getPackage() . '_' . $theme->getTheme();
                    $config['assetic_configuration']['modules'][$moduleName]['root_path'][] = __DIR__ . '/../../../../../design/'.$theme->getArea().'/'.$theme->getPackage().'/'.$theme->getTheme().'/assets';
                    $config['assetic_configuration']['modules'][$moduleName]['collections']['admin_images']['assets'] = array('images/screenshots/*.jpg', 'images/screenshots/*.gif', 'images/screenshots/*.png');
                    $config['assetic_configuration']['modules'][$moduleName]['collections']['admin_images']['options']['output'] = 'theme/';
                    $config['assetic_configuration']['modules'][$moduleName]['collections']['admin_images']['options']['move_raw'] = 'true';
                }
            }*/
        }

        $e->getConfigListener()->setMergedConfig($config);
    }

    public function onBootstrap(EventInterface $e)
    {
        $serviceManager = $e->getApplication()->getServiceManager();
        $this->setServiceManager($serviceManager);

        $options = $serviceManager->get('playgroundcore_module_options');
        $translator = $serviceManager->get('translator');
        $locale = $options->getLocale();
        if (!empty($locale)) {
            //translator
            $translator->setLocale($locale);

            // plugins
            $translate = $serviceManager->get('viewhelpermanager')->get('translate');
            $translate->getTranslator()->setLocale($locale);
        }

        AbstractValidator::setDefaultTranslator($translator, 'playgrounddesign');
        
        // Start the session container
        $config = $e->getApplication()->getServiceManager()->get('config');
        
        /**
         * This listener gives the possibility to select the layout on module / controller / action level !
         * Just configure it in any module config or autoloaded config.
         */
        $e->getApplication()->getEventManager()->getSharedManager()->attach('Zend\Mvc\Controller\AbstractActionController', 'dispatch', function ($e) {
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

/*
                echo '$controllerClass : ' . $controllerClass . '<br/>';
                echo '$moduleName : ' .$moduleName. '<br/>';
                echo '$routeName : '.$routeName. '<br/>';
                echo '$areaName : '.$areaName. '<br/>';
                echo '$controllerName : ' .$controllerName. '<br/>';
                echo '$actionName : ' . $actionName. '<br/>';
               
*/
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
        $e->getApplication()->getEventManager()->attach(\Zend\Mvc\MvcEvent::EVENT_RENDER, function (\Zend\Mvc\MvcEvent $e) use ($serviceManager) {
        
            $viewModel = $e->getViewModel();
            $match = $e->getRouteMatch();
            $area = isset($match)? $match->getParam('area', ''):'';
            $sm = $e->getApplication()->getServiceManager();
            if ($match) {
                $title = $match->getParam('title');
                $action = $match->getParam('action');
                $controller = explode('\\', $match->getParam('controller'));
                $controller = end($controller);
                $headTitleHelper = $sm->get('viewHelperManager')->get('headTitle');

                if (empty($title)) {
                    $title = $controller . '-' . $action;
                }
                $title = $sm->get('translator')->translate($title, 'routes');

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

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/../../src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                
                'frontendUrl' => function ($sm) {
                    $serviceLocator = $sm->getServiceLocator();
                    $view_helper =  new View\Helper\FrontendUrl();
                    $router = \Zend\Console\Console::isConsole() ? 'HttpRouter' : 'Router';
                    $view_helper->setRouter($serviceLocator->get($router));
                
                    $match = $sm->getServiceLocator()->get('application')->getMvcEvent()->getRouteMatch();
                
                    if ($match instanceof \Zend\Mvc\Router\Http\RouteMatch) {
                        $view_helper->setRouteMatch($match);
                    }
                
                    return $view_helper;
                },

                // This admin navigation layer gives the authentication layer based on BjyAuthorize ;)
                'adminMenu' => function ($sm) {
                    $nav = $sm->get('navigation')->menu('admin_navigation');
                    $serviceLocator = $sm->getServiceLocator();
                    $nav->setUlClass('nav')
                        ->setMaxDepth(10)
                        ->setRenderInvisible(false);

                    return $nav;
                },

                'adminAssetPath' => function ($sm) {
                    $config = $sm->getServiceLocator()->has('Config') ? $sm->getServiceLocator()->get('Config') : array();
                    $helper  = new View\Helper\AdminAssetPath;
                    if (isset($config['view_manager']) && isset($config['view_manager']['base_path'])) {
                        $basePath = $config['view_manager']['base_path'];
                    } else {
                        $basePath = $sm->getServiceLocator()->get('Request')->getBasePath();
                    }
                    $helper->setBasePath($basePath);
                    return $helper;
                },

                'frontendAssetPath' => function ($sm) {
                    $config = $sm->getServiceLocator()->has('Config') ? $sm->getServiceLocator()->get('Config') : array();
                    $helper  = new View\Helper\FrontendAssetPath;
                    if (isset($config['view_manager']) && isset($config['view_manager']['base_path'])) {
                        $basePath = $config['view_manager']['base_path'];
                    } else {
                        $basePath = $sm->getServiceLocator()->get('Request')->getBasePath();
                    }
                    $helper->setBasePath($basePath);
                    return $helper;
                },

                'libAssetPath' => function ($sm) {
                    $config = $sm->getServiceLocator()->has('Config') ? $sm->getServiceLocator()->get('Config') : array();
                    $helper  = new View\Helper\LibAssetPath;
                    if (isset($config['view_manager']) && isset($config['view_manager']['base_path'])) {
                        $basePath = $config['view_manager']['base_path'];
                    } else {
                        $basePath = $sm->getServiceLocator()->get('Request')->getBasePath();
                    }
                    $helper->setBasePath($basePath);
                    return $helper;
                },

                'facebookUrl' => function ($sm) {
                    $locator = $sm->getServiceLocator();
                    $fbUrl = null;
                    $config = $locator->get('config');
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
                    $locator = $sm->getServiceLocator();
                    $rssUrl = '';
                    $config = $locator->get('config');
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
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                // overriding wilmogrod Assetic definition
                'AsseticBundle\Configuration' => function ($sm) {
                    $configuration = $sm->get('Configuration');
                    return new Assetic\Configuration($configuration['assetic_configuration']);
                },
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

                'playgrounddesign_theme_form' => function ($sm) {
                    $translator = $sm->get('translator');
                    $form = new Form\Admin\Theme(null, $sm, $translator);
                    $theme = new Entity\Theme();
                    $form->setInputFilter($theme->getInputFilter());

                    return $form;
                },

                'playgrounddesign_company_form' => function ($sm) {
                    $translator = $sm->get('translator');
                    $form = new Form\Admin\Company(null, $sm, $translator);
                    $company = new Entity\Company();
                    $form->setInputFilter($company->getInputFilter());
                    return $form;
                }
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
