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

class Module implements
    AutoloaderProviderInterface,
    BootstrapListenerInterface,
    ConfigProviderInterface,
    ServiceProviderInterface,
    ViewHelperProviderInterface
{

    public $themeMapper;

    public function init(ModuleManager $manager)
    {

        $eventManager = $manager->getEventManager();
        /*
         * This event will exist in ZF 2.3.0. I'll then use it to change the config before it's cached
         * The change will apply to 'template_path_stack' and 'assetic_configuration'
         * These 2 config take part in the Playground Theme Management
         */
        $eventManager->attach(\Zend\ModuleManager\ModuleEvent::EVENT_MERGE_CONFIG, array($this, 'onMergeConfig'));
    }

    /*
     * This function will be used once 'EVENT_MERGE_CONFIG' will exist in ZF 2.3
     */
    public function onMergeConfig($e)
    {
       $config = $e->getConfigListener()->getMergedConfig(false);

        // Recuperation du theme pour le backoffice.
        if (isset($config['design'])) {
            $configHasChanged = false;
            $viewResolverPathStack = $config['template_path_stack'];
            if (isset($config['design']['admin']) && isset($config['design']['admin']['package']) && isset($config['design']['admin']['theme'])) {

                $adminPath = __DIR__ . '/../../../../../design/admin/'. $config['design']['admin']['package'] .'/'. $config['design']['admin']['theme'];

                $themeMaper = $this->getThemeMapper();
                $themesActivated = $themeMaper->findBy(array('is_active' => true, 'type' => 'admin'));
                $themeActivated = $themesActivated[0];

                // Surchage par le theme qui est activé en base de donnée
                if(!empty($themeActivated)) {
                    $frontendPath = __DIR__ . '/../../../../../design/admin/'. $themeActivated['package'] .'/'. $themeActivated['theme'];
                }

                // I get the Theme definition file and apply a check on the parent theme.
                // TODO : Apply recursion to this stuff.
                $adminThemePath = $adminPath . '/theme.php';
                if (is_file($adminThemePath) && is_readable($adminThemePath)) {
                    $configTheme = new \Zend\Config\Config(include $adminThemePath);

                    if (isset($configTheme['design']['package']['theme']['parent'])) {
                        $stack = array();
                        $parentTheme = explode('_', $configTheme['design']['package']['theme']['parent']);
                        if (!(strtolower($parentTheme[0]) === 'playground' && strtolower($parentTheme[1]) === 'base')) {
                            // The parent for this theme is not the base one. I remove the base admin paths
                            foreach($viewResolverPathStack->getPaths() as $path){
                                if(!$result = preg_match('/\/admin\/$/',$path,$matches)){
                                  $stack[] = $path;
                                }
                            }
                            $parentPath = __DIR__ . '/../../../../../design/admin/'. $parentTheme[0] .'/'. $parentTheme[1];
                            $stack[] = $parentPath;
                            $viewResolverPathStack->clearPaths();
                            $viewResolverPathStack->addPaths($stack);
                        }
                    } else {
                        // There is no parent to this theme. I remove the base admin paths
                        foreach ($viewResolverPathStack->getPaths() as $path) {
                            if (!$result = preg_match('/\/admin\/$/',$path,$matches)) {
                                $stack[] = $path;
                            }
                        }
                        $viewResolverPathStack->clearPaths();
                        $viewResolverPathStack->addPaths($stack);
                    }
                }

                $pathStack = array($adminPath);

                // Assetic pour les CSS
                $config['assetic_configuration']['modules']['admin']['root_path'][] = $adminPath . '/assets';

                // Resolver des templates phtml
                $viewResolverPathStack->addPaths($pathStack);

                //print_r($viewResolverPathStack->getPaths());

                $assets = $adminPath . '/assets.php';
                if (is_file($assets) && is_readable($assets)) {
                    $configAssets = new \Zend\Config\Config(include $assets);
                    $config = array_replace_recursive($config, $configAssets->toArray());
                    $configHasChanged = true;
                }

                $layout = $adminPath . '/layout.php';
                if (is_file($layout) && is_readable($layout)) {
                    $configLayout = new \Zend\Config\Config(include $layout);
                    $config = array_replace_recursive($config, $configLayout->toArray());
                    $configHasChanged = true;
                }
            }
            if (isset($config['design']['frontend']) && isset($config['design']['frontend']['package']) && isset($config['design']['frontend']['theme'])) {

                $frontendPath = __DIR__ . '/../../../../../design/frontend/'. $config['design']['frontend']['package'] .'/'. $config['design']['frontend']['theme'];
                
                $themeMaper = $this->getThemeMapper();
                $themesActivated = $themeMaper->findBy(array('is_active' => true, 'type' => 'frontend'));
                $themeActivated = $themesActivated[0];

                // Surchage par le theme qui est activé en base de donnée
                if(!empty($themeActivated)) {
                    $frontendPath = __DIR__ . '/../../../../../design/frontend/'. $themeActivated['package'] .'/'. $themeActivated['theme'];
                }


                $frontendThemePath = $frontendPath . '/theme.php';
                if (is_file($frontendThemePath) && is_readable($frontendThemePath)) {
                    $configTheme = new \Zend\Config\Config(include $frontendThemePath);

                    if (isset($configTheme['design']['package']['theme']['parent'])) {
                        $stack = array();
                        $parentTheme = explode('_', $configTheme['design']['package']['theme']['parent']);
                        if (!(strtolower($parentTheme[0]) === 'playground' && strtolower($parentTheme[1]) === 'base')) {
                            // The parent for this theme is not the base one. I remove the base frontend paths
                            foreach ($viewResolverPathStack->getPaths() as $path) {
                                if (!$result = preg_match('/\/frontend\/$/',$path,$matches)) {
                                    $stack[] = $path;
                                }
                            }
                            $parentPath = __DIR__ . '/../../../../../design/frontend/'. $parentTheme[0] .'/'. $parentTheme[1];
                            $stack[] = $parentPath;
                            $viewResolverPathStack->clearPaths();
                            $viewResolverPathStack->addPaths($stack);
                        }
                    } else {
                        // There is no parent to this theme. I remove the base frontend paths
                        foreach ($viewResolverPathStack->getPaths() as $path) {
                            if (!$result = preg_match('/\/frontend\/$/',$path,$matches)) {
                                $stack[] = $path;
                            }
                        }
                        $viewResolverPathStack->clearPaths();
                        $viewResolverPathStack->addPaths($stack);
                    }
                }

                $pathStack = array($frontendPath);
                // Assetic pour les CSS
                $config['assetic_configuration']['modules']['frontend']['root_path'][] = $frontendPath . '/assets';

                $viewResolverPathStack->addPaths($pathStack);

                $assets = $frontendPath . '/assets.php';
                if (is_file($assets) && is_readable($assets)) {
                    $configAssets = new \Zend\Config\Config(include $assets);
                    $config = array_replace_recursive($config, $configAssets->toArray() );
                    $configHasChanged = true;
                }

                $layout = $frontendPath . '/layout.php';
                if (is_file($layout) && is_readable($layout)) {
                    $configLayout = new \Zend\Config\Config(include $layout);
                    $config = array_replace_recursive($config, $configLayout->toArray() );
                    $configHasChanged = true;
                }
            }


            if ($configHasChanged) {
                $e->getApplication()->getServiceManager()->setAllowOverride(true);
                $e->getApplication()->getServiceManager()->setService('config', $config);
            }
        }

        $e->getConfigListener()->setMergedConfig($config);


        // do something with the above!
    }

    public function onBootstrap(EventInterface $e)
    {
        $serviceManager = $e->getApplication()->getServiceManager();

        /* Set the translator for default validation messages
         * I've copy/paste the Validator messages from ZF2 and placed them in a correct path : PlaygroundDesign
        * TODO : Centraliser la trad pour les Helper et les Plugins
        */
        $translator = $serviceManager->get('translator');

        //Translation based on Browser's locale
        //$translator->setLocale(\Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']));

        // positionnement de la langue pour les traductions de date avec strftime
        setlocale(LC_TIME, "fr_FR", 'fr_FR.utf8', 'fra');

        AbstractValidator::setDefaultTranslator($translator,'playgrounddesign');


        // Start the session container
        $config = $e->getApplication()->getServiceManager()->get('config');

        // Design management : template and assets management
        if(isset($config['design'])){
            $configHasChanged = false;
            $viewResolverPathStack = $e->getApplication()->getServiceManager()->get('ViewTemplatePathStack');
            if(isset($config['design']['admin']) && isset($config['design']['admin']['package']) && isset($config['design']['admin']['theme'])){

                $adminPath = __DIR__ . '/../../../../../design/admin/'. $config['design']['admin']['package'] .'/'. $config['design']['admin']['theme'];

                $themeMaper = $this->getThemeMapper($serviceManager);
                $themesActivated = $themeMaper->findBy(array('is_active' => true, 'type' => 'admin'));
                $themeActivated = $themesActivated[0];

                // Surchage par le theme qui est activé en base de donnée
               /* if(!empty($themeActivated)) {
                    $adminPath = __DIR__ . '/../../../../../design/admin/'. $themeActivated->getPackage() .'/'. $themeActivated->getTheme();
                }*/

                // I get the Theme definition file and apply a check on the parent theme.
                // TODO : Apply recursion to this stuff.
                $adminThemePath = $adminPath . '/theme.php';
                if(is_file($adminThemePath) && is_readable($adminThemePath)){
                    $configTheme = new \Zend\Config\Config(include $adminThemePath);

                    if (isset($configTheme['design']['package']['theme']['parent'])){
                        $stack = array();
                        $parentTheme = explode('_', $configTheme['design']['package']['theme']['parent']);
                        if(!(strtolower($parentTheme[0]) === 'playground' && strtolower($parentTheme[1]) === 'base')){
                            // The parent for this theme is not the base one. I remove the base admin paths
                            foreach($viewResolverPathStack->getPaths() as $path){
                                if(!$result = preg_match('/\/admin\/$/',$path,$matches)){
                                  $stack[] = $path;
                                }
                            }
                            $parentPath = __DIR__ . '/../../../../../design/admin/'. $parentTheme[0] .'/'. $parentTheme[1];
                            $stack[] = $parentPath;
                            $viewResolverPathStack->clearPaths();
                            $viewResolverPathStack->addPaths($stack);
                        }
                    } else {
                        // There is no parent to this theme. I remove the base admin paths
                        foreach($viewResolverPathStack->getPaths() as $path){
                            if(!$result = preg_match('/\/admin\/$/',$path,$matches)){
                                $stack[] = $path;
                            }
                        }
                        $viewResolverPathStack->clearPaths();
                        $viewResolverPathStack->addPaths($stack);
                    }
                }

                $pathStack = array($adminPath);

                // Assetic pour les CSS
                $config['assetic_configuration']['modules']['admin']['root_path'][] = $adminPath . '/assets';

                // Resolver des templates phtml
                $viewResolverPathStack->addPaths($pathStack);

                //print_r($viewResolverPathStack->getPaths());

                $assets = $adminPath . '/assets.php';
                if(is_file($assets) && is_readable($assets)){
                    $configAssets = new \Zend\Config\Config(include $assets);
                    $config = array_replace_recursive($config, $configAssets->toArray());
                    $configHasChanged = true;
                }

                $layout = $adminPath . '/layout.php';
                if(is_file($layout) && is_readable($layout)){
                    $configLayout = new \Zend\Config\Config(include $layout);
                    $config = array_replace_recursive($config, $configLayout->toArray());
                    $configHasChanged = true;
                }



                $themes = $themeMaper->findBy(array('type' => 'admin'));
                foreach ($themes as $theme) {
                    $config['assetic_configuration']['modules'][$theme->getTitle()]['root_path'][] = __DIR__ . '/../../../../../design/'.$theme->getType().'/'.$theme->getPackage().'/'.$theme->getTheme().'/assets';
                    $config['assetic_configuration']['modules'][$theme->getTitle()]['collections']['admin_images']['assets'] = array('images/screenshots/*.jpg');
                    $config['assetic_configuration']['modules'][$theme->getTitle()]['collections']['admin_images']['options']['output'] = 'theme/';
                    $config['assetic_configuration']['modules'][$theme->getTitle()]['collections']['admin_images']['options']['move_raw'] = 'true';
                }
               
                $configHasChanged = true;
            }
            if(isset($config['design']['frontend']) && isset($config['design']['frontend']['package']) && isset($config['design']['frontend']['theme'])){
                $frontendPath = __DIR__ . '/../../../../../design/frontend/'. $config['design']['frontend']['package'] .'/'. $config['design']['frontend']['theme'];

                $themeMaper = $this->getThemeMapper($serviceManager);
                $themesActivated = $themeMaper->findBy(array('is_active' => true, 'type' => 'frontend'));
                $themeActivated = $themesActivated[0];

                // Surchage par le theme qui est activé en base de donnée
               /* if(!empty($themeActivated)) {
                    $frontendPath = __DIR__ . '/../../../../../design/frontend/'. $themeActivated->getPackage().'/'. $themeActivated->getTheme();
                }*/

                $frontendThemePath = $frontendPath . '/theme.php';
                if(is_file($frontendThemePath) && is_readable($frontendThemePath)){
                    $configTheme = new \Zend\Config\Config(include $frontendThemePath);

                    if (isset($configTheme['design']['package']['theme']['parent'])){
                        $stack = array();
                        $parentTheme = explode('_', $configTheme['design']['package']['theme']['parent']);
                        if(!(strtolower($parentTheme[0]) === 'playground' && strtolower($parentTheme[1]) === 'base')){
                            // The parent for this theme is not the base one. I remove the base frontend paths
                            foreach($viewResolverPathStack->getPaths() as $path){
                                if(!$result = preg_match('/\/frontend\/$/',$path,$matches)){
                                    $stack[] = $path;
                                }
                            }
                            $parentPath = __DIR__ . '/../../../../../design/frontend/'. $parentTheme[0] .'/'. $parentTheme[1];
                            $stack[] = $parentPath;
                            $viewResolverPathStack->clearPaths();
                            $viewResolverPathStack->addPaths($stack);
                        }
                    } else {
                        // There is no parent to this theme. I remove the base frontend paths
                        foreach($viewResolverPathStack->getPaths() as $path){
                            if(!$result = preg_match('/\/frontend\/$/',$path,$matches)){
                                $stack[] = $path;
                            }
                        }
                        $viewResolverPathStack->clearPaths();
                        $viewResolverPathStack->addPaths($stack);
                    }
                }

                $pathStack = array($frontendPath);
                // Assetic pour les CSS
                $config['assetic_configuration']['modules']['frontend']['root_path'][] = $frontendPath . '/assets';

                $viewResolverPathStack->addPaths($pathStack);

                $assets = $frontendPath . '/assets.php';
                if(is_file($assets) && is_readable($assets)){
                    $configAssets = new \Zend\Config\Config(include $assets);
                    $config = array_replace_recursive($config, $configAssets->toArray() );
                    $configHasChanged = true;
                }

                $layout = $frontendPath . '/layout.php';
                if(is_file($layout) && is_readable($layout)){
                    $configLayout = new \Zend\Config\Config(include $layout);
                    $config = array_replace_recursive($config, $configLayout->toArray() );
                    $configHasChanged = true;
                }
            }
             
            $themes = $themeMaper->findBy(array('type' => 'frontend'));
            foreach ($themes as $theme) {
                $config['assetic_configuration']['modules'][$theme->getTitle()]['root_path'][] = __DIR__ . '/../../../../../design/'.$theme->getType().'/'.$theme->getPackage().'/'.$theme->getTheme().'/assets';
                $config['assetic_configuration']['modules'][$theme->getTitle()]['collections']['frontend_images']['assets'] = array('images/screenshots/*.jpg');
                $config['assetic_configuration']['modules'][$theme->getTitle()]['collections']['frontend_images']['options']['output'] = 'theme/';
                $config['assetic_configuration']['modules'][$theme->getTitle()]['collections']['frontend_images']['options']['move_raw'] = 'true';
            }
           
            $configHasChanged = true;

            if($configHasChanged){
                $e->getApplication()->getServiceManager()->setAllowOverride(true);
                $e->getApplication()->getServiceManager()->setService('config', $config);
            }
        }

        /**
         * This listener gives the possibility to select the layout on module / controller / action level !
         * Just configure it in any module config or autoloaded config.
         */
        $e->getApplication()->getEventManager()->getSharedManager()->attach('Zend\Mvc\Controller\AbstractActionController', 'dispatch', function($e) {
            $config     = $e->getApplication()->getServiceManager()->get('config');
            if (isset($config['core_layout'])) {
                $controller      = $e->getTarget();
                $controllerClass = get_class($controller);
                $moduleName      = strtolower(substr($controllerClass, 0, strpos($controllerClass, '\\')));
                $match           = $e->getRouteMatch();
                $routeName       = $match->getMatchedRouteName();
                $areaName        = (strpos($routeName, '/'))?substr($routeName, 0, strpos($routeName, '/')):$routeName;
                $controllerName  = $match->getParam('controller', 'not-found');
                $actionName      = $match->getParam('action', 'not-found');
                $channel         = $match->getParam('channel', 'not-found');
                $viewModel       = $e->getViewModel();


                /**
                 * Assign the correct layout
                 */

                if (isset($config['core_layout'][$areaName]['modules'][$moduleName]['controllers'][$controllerName]['actions'][$actionName]['channel'][$channel]['layout'])) {
                    //print_r($config['core_layout'][$areaName]['modules'][$moduleName]['controllers'][$controllerName]['actions'][$actionName]['channel'][$channel]['layout']);
                    $controller->layout($config['core_layout'][$areaName]['modules'][$moduleName]['controllers'][$controllerName]['actions'][$actionName]['channel'][$channel]['layout']);
                } elseif (isset($config['core_layout'][$areaName]['modules'][$moduleName]['controllers'][$controllerName]['actions'][$actionName]['layout'])) {
                    //print_r($config['core_layout'][$areaName]['modules'][$moduleName]['controllers'][$controllerName]['actions'][$actionName]['layout']);
                    $controller->layout($config['core_layout'][$areaName]['modules'][$moduleName]['controllers'][$controllerName]['actions'][$actionName]['layout']);
                } elseif (isset($config['core_layout'][$areaName]['modules'][$moduleName]['controllers'][$controllerName]['channel'][$channel]['layout'])) {
                    //print_r($config['core_layout'][$areaName]['modules'][$moduleName]['controllers'][$controllerName]['channel'][$channel]['layout']);
                    $controller->layout($config['core_layout'][$areaName]['modules'][$moduleName]['controllers'][$controllerName]['channel'][$channel]['layout']);
                } elseif (isset($config['core_layout'][$areaName]['modules'][$moduleName]['controllers'][$controllerName]['layout'])) {
                    //print_r($config['core_layout'][$areaName]['modules'][$moduleName]['controllers'][$controllerName]['layout']);
                    $controller->layout($config['core_layout'][$areaName]['modules'][$moduleName]['controllers'][$controllerName]['layout']);
                } elseif (isset($config['core_layout'][$areaName]['modules'][$moduleName]['channel'][$channel]['layout'])) {
                    //print_r($config['core_layout'][$areaName]['modules'][$moduleName]['channel'][$channel]['layout']);
                    $controller->layout($config['core_layout'][$areaName]['modules'][$moduleName]['channel'][$channel]['layout']);
                } elseif (isset($config['core_layout'][$areaName]['modules'][$moduleName]['layout'])) {
                    //print_r($config['core_layout'][$areaName]['modules'][$moduleName]['layout']);
                    $controller->layout($config['core_layout'][$areaName]['modules'][$moduleName]['layout']);
                } elseif (isset($config['core_layout'][$areaName]['channel'][$channel]['layout'])) {
                    //print_r($config['core_layout'][$areaName]['channel'][$channel]['layout']);
                    $controller->layout($config['core_layout'][$areaName]['channel'][$channel]['layout']);
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

                // This admin navigation layer gives the authentication layer based on BjyAuthorize ;)
                'adminMenu' => function($sm){
                    $nav = $sm->get('navigation')->menu('admin_navigation');
                    $serviceLocator = $sm->getServiceLocator();
                    $nav->setUlClass('nav')
                        ->setMaxDepth(10)
                        ->setRenderInvisible(false);

                    return $nav;
                },

                'adminAssetPath' => function($sm) {
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

                'frontendAssetPath' => function($sm) {
                    $config = $sm->getServiceLocator()->has('Config') ? $sm->getServiceLocator()->get('Config') : array();
                    $helper  = new View\Helper\FrontendAssetPath;
                    if (isset($config['view_manager']) && isset($config['view_manager']['base_path'])) {
                        $basePath = $config['view_manager']['base_path'];
                    } else {
                        $basePath = $sm->getServiceLocator()->get('Request')->getBasePath();
                    }
                    $helper->setBasePath($basePath);
                    return $helper;
                }
            ),
        );

    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'admin_navigation' => 'PlaygroundDesign\Service\AdminNavigationFactory',
                'playgrounddesign_module_options' => function  ($sm) {
                    $config = $sm->get('Configuration');

                    return new Options\ModuleOptions(isset($config['playgrounddesign']) ? $config['playgrounddesign'] : array());
                },
                'playgrounddesign_theme_mapper' => function  ($sm) {
                    
                    return new Mapper\Theme($sm->get('playgrounddesign_doctrine_em'), $sm->get('playgrounddesign_module_options'));
                },
                'playgrounddesign_theme_form' => function  ($sm) {
                    $translator = $sm->get('translator');
                    $form = new Form\Admin\Theme(null, $sm, $translator);
                    $theme = new Entity\Theme();
                    $form->setInputFilter($theme->getInputFilter());

                    return $form;
                }
            ),
            'aliases' => array(
                'playgrounddesign_doctrine_em' => 'doctrine.entitymanager.orm_default'
            ),
            'invokables' => array(
                'playgrounddesign_theme_service' => 'PlaygroundDesign\Service\Theme'
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
}