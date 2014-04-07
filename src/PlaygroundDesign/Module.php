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
        $eventManager->attach(\Zend\ModuleManager\ModuleEvent::EVENT_MERGE_CONFIG, array($this, 'onMergeConfig'));
    }

    /*
     * This function will be used once 'EVENT_MERGE_CONFIG' will exist in ZF 2.3
     */
    public function onMergeConfig($e)
    {
        $config = $e->getConfigListener()->getMergedConfig(false);
        $e->getConfigListener()->setMergedConfig($config);
        // do something with the above!
    }

    public function onBootstrap(EventInterface $e)
    {
        $serviceManager = $e->getApplication()->getServiceManager();

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

        // positionnement de la langue pour les traductions de date avec strftime
        setlocale(LC_TIME, "fr_FR", 'fr_FR.utf8', 'fra');

        AbstractValidator::setDefaultTranslator($translator,'playgrounddesign');
        $tables = array();


        // Start the session container
        $config = $e->getApplication()->getServiceManager()->get('config');

        // overriding the theme definition in config if present in database
        if (PHP_SAPI !== 'cli') {

            $themeMapper = $this->getThemeMapper($serviceManager);

            $themesActivated = $themeMapper->findBy(array('is_active' => true, 'area' => 'admin'));
            if (!empty($themesActivated)) {
                $themeActivated = $themesActivated[0];

                // Surchage par le theme qui est activé en base de donnée
                if(!empty($themeActivated)) {
                    //$adminPath = __DIR__ . '/../../../../../design/admin/'. $themeActivated->getPackage() .'/'. $themeActivated->getTheme();
                    $config['design']['admin']['package'] = $themeActivated->getPackage();
                    $config['design']['admin']['theme'] = $themeActivated->getTheme();
                }
            }

            $themesActivated = $themeMapper->findBy(array('is_active' => true, 'area' => 'frontend'));
            if (!empty($themesActivated)) {
                $themeActivated = $themesActivated[0];

                // Surchage par le theme qui est activé en base de donnée
                if(!empty($themeActivated)) {
                    //$adminPath = __DIR__ . '/../../../../../design/admin/'. $themeActivated->getPackage() .'/'. $themeActivated->getTheme();
                    $config['design']['frontend']['package'] = $themeActivated->getPackage();
                    $config['design']['frontend']['theme'] = $themeActivated->getTheme();
                }
            }
        }

        // Design management : template and assets management
        if(isset($config['design'])){

        	$viewResolverPathStack = $e->getApplication()->getServiceManager()->get('ViewTemplatePathStack');
        	if(isset($config['design']['admin']['theme'])){

        	    $themeHierarchy = array();
                $hasParent = true;
                $parentTheme = array($config['design']['admin']['package'], $config['design']['admin']['theme']);

        		while($hasParent){
            		// I get the Theme definition file and apply a check on the parent theme.
            		// TODO : Apply recursion to this stuff.
        		    $hasParent = false;
        		    $adminPath = __DIR__ . '/../../../../../design/admin/'. $parentTheme[0] .'/'. $parentTheme[1];
        		    $themeId = $parentTheme[0] .'_'. $parentTheme[1];

        		    //echo $themeId . "<br>";

        		    if(!(strtolower($themeId) === 'playground_base')){
                		$adminThemePath = $adminPath . '/theme.php';

                		if(is_file($adminThemePath) && is_readable($adminThemePath)){
                		    $configTheme                      = new \Zend\Config\Config(include $adminThemePath);
                		    $themeHierarchy[$themeId]['path'] = $adminPath;

                		    $pathStack = array($adminPath);
                		    $themeHierarchy[$themeId]['template_path']= $pathStack;

                		    $assets = $adminPath . '/assets.php';
                		    if(is_file($assets) && is_readable($assets)){
                		        $configAssets = new \Zend\Config\Config(include $assets);
                		        $themeHierarchy[$themeId]['assets'] = $configAssets->toArray();
                		    }

                		    $layout = $adminPath . '/layout.php';
                		    if(is_file($layout) && is_readable($layout)){
                		        $configLayout = new \Zend\Config\Config(include $layout);
                		        $themeHierarchy[$themeId]['layout'] = $configLayout->toArray();
                		    }

                		    if (isset($configTheme['design']['package']['theme']['parent'])){
                		        $parentTheme = explode('_', $configTheme['design']['package']['theme']['parent']);
                                if ($parentTheme == $themeId) {
                                    $hasParent = false;
                                }
                		        $hasParent = true;
                		    }
                		}
        		    } else {

        		        $stack = array();
        		        foreach($viewResolverPathStack->getPaths() as $path){
        		            if($result = preg_match('/\/admin\/$/',$path,$matches)){
        		                $stack[] = $path;
        		            }
        		        }
        		        $themeHierarchy[$themeId]['template_path']= array_reverse($stack);


        		        if(isset($config['assetic_configuration']['modules']['admin'])){
        		            $asseticConfig = array('assetic_configuration' => array(
        		                'modules' => array('admin' => $config['assetic_configuration']['modules']['admin']),
        		                'routes' => array('admin.*' => $config['assetic_configuration']['routes']['admin.*'])
        		            ));

        		            $themeHierarchy[$themeId]['assets'] = $asseticConfig;
        		        }

        		        if(isset($config['core_layout']['admin'])){
        		            $themeHierarchy[$themeId]['layout'] = $config['core_layout']['admin'];
        		        }
        		    }
        		}

        		// I reverse the array of $themeHierarchy to have Parents -> children
        		$themeHierarchy = array_reverse($themeHierarchy);

        		// We remove the former config
        		$stack = array();
        		foreach($viewResolverPathStack->getPaths() as $path){
        		    if(!$result = preg_match('/\/admin\/$/',$path,$matches)){
        		        $stack[] = $path;
        		    }
        		}

        		$viewResolverPathStack->clearPaths();
        		$viewResolverPathStack->addPaths(array_reverse($stack));

        		/*echo "tous paths sauf admin<br>";
        		print_r($viewResolverPathStack->getPaths());
                echo "<br><br>";*/

        		// removing default assetic configuration
        		if(isset($config['assetic_configuration']['modules']['admin'])){
        		    unset($config['assetic_configuration']['modules']['admin']);
        		    unset($config['assetic_configuration']['routes']['admin.*']);
        		}

        		// removing default layout configuration
        		if(isset($config['core_layout']['admin'])){
        		    unset($config['core_layout']['admin']);
        		}

        		//I then recreate the config
        		foreach($themeHierarchy as $theme=>$tab){
        		    //echo "<h3>" . $k . ":</h3>";
        		    if(isset($tab['layout'])){
        		      $config = array_replace_recursive($config, $tab['layout'] );
        		    }

        		    if(isset($tab['assets'])){
        		        $config = array_replace_recursive($config, $tab['assets'] );
        		    }

        		    if(isset($tab['template_path'])){
        		        $viewResolverPathStack->addPaths($tab['template_path']);
        		    }

        		    if(isset($tab['path']) && isset($config['assetic_configuration']['modules']['admin'])){
        		        $config['assetic_configuration']['modules']['admin']['root_path'][] = $tab['path'] . '/assets';
        		    }

        		}

        		/*echo "tous paths avec hierarchie admin<br>";
        		print_r($viewResolverPathStack->getPaths());
                echo "<br><br>";*/
        	}

        	if(isset($config['design']['frontend']['theme'])){

        	    $themeHierarchy = array();
        	    $hasParent = true;
        	    $parentTheme = array($config['design']['frontend']['package'], $config['design']['frontend']['theme']);

        	    while($hasParent){
        	        // I get the Theme definition file and apply a check on the parent theme.
        	        // TODO : Apply recursion to this stuff.
        	        $hasParent = false;
        	        $frontendPath = __DIR__ . '/../../../../../design/frontend/'. $parentTheme[0] .'/'. $parentTheme[1];
        	        $themeId = $parentTheme[0] .'_'. $parentTheme[1];

        	        //echo $themeId . "<br>";

        	        if(!(strtolower($themeId) === 'playground_base')){
        	            $frontendThemePath = $frontendPath . '/theme.php';

        	            if(is_file($frontendThemePath) && is_readable($frontendThemePath)){
        	                $configTheme                      = new \Zend\Config\Config(include $frontendThemePath);
        	                $themeHierarchy[$themeId]['path'] = $frontendPath;

        	                $pathStack = array($frontendPath);
        	                $themeHierarchy[$themeId]['template_path']= $pathStack;

        	                $assets = $frontendPath . '/assets.php';
        	                if(is_file($assets) && is_readable($assets)){
                		        $configAssets = new \Zend\Config\Config(include $assets);
                		        $themeHierarchy[$themeId]['assets'] = $configAssets->toArray();
                		    }

        	                $layout = $frontendPath . '/layout.php';
        	                if(is_file($layout) && is_readable($layout)){
                		        $configLayout = new \Zend\Config\Config(include $layout);
                		        $themeHierarchy[$themeId]['layout'] = $configLayout->toArray();
                		    }

        	                if (isset($configTheme['design']['package']['theme']['parent'])){
        	                    $parentTheme = explode('_', $configTheme['design']['package']['theme']['parent']);
                                if ($parentTheme == $themeId) {
                                    $hasParent = false;
                                }
        	                    $hasParent = true;
        	                }
        	            }
        	        } else {

        	            $stack = array();
        	            foreach($viewResolverPathStack->getPaths() as $path){
        	                if($result = preg_match('/\/frontend\/$/',$path,$matches)){
        	                    $stack[] = $path;
        	                }
        	            }

        	            $themeHierarchy[$themeId]['template_path']= array_reverse($stack);

        	            /*echo "tous paths frontend par defaut<br>";
        	            print_r($viewResolverPathStack->getPaths());
        	            echo "<br><br>";*/

        	            if(isset($config['assetic_configuration']['modules']['frontend'])){
        	                $asseticConfig = array('assetic_configuration' => array(
        		                'modules' => array('frontend' => $config['assetic_configuration']['modules']['frontend']),
        		                'routes' => array('frontend.*' => $config['assetic_configuration']['routes']['frontend.*'])
        		            ));

        		            $themeHierarchy[$themeId]['assets'] = $asseticConfig;
        		            /*echo '<pre>';
        		            print_r($asseticConfig['assetic_configuration']['routes']);
        		            echo '</pre>';*/
        	            }

        	            if(isset($config['core_layout']['frontend'])){
        	                $themeHierarchy[$themeId]['layout'] = $config['core_layout']['frontend'];
        	            }
        	        }
        	    }

        	    $themeHierarchy = array_reverse($themeHierarchy);

        	    // We remove the former config
        	    $stack = array();
        	    foreach($viewResolverPathStack->getPaths() as $path){
        	        if(!$result = preg_match('/\/frontend\/$/',$path,$matches)){
        	            $stack[] = $path;
        	        }
        	    }

        	    $viewResolverPathStack->clearPaths();

        	    $viewResolverPathStack->addPaths(array_reverse($stack));

        	    /*echo "tous paths sauf frontend<br>";
        	    print_r($viewResolverPathStack->getPaths());
        	    echo "<br><br>";*/


        	    // removing default assetic configuration
        	    if(isset($config['assetic_configuration']['modules']['frontend'])){
        	        unset($config['assetic_configuration']['modules']['frontend']);
        	        unset($config['assetic_configuration']['routes']['frontend.*']);
        	    }

        	    // removing default layout configuration
        	    if(isset($config['core_layout']['frontend'])){
        	        unset($config['core_layout']['frontend']);
        	    }

        	    //I then recreate the config
        	    foreach($themeHierarchy as $theme=>$tab){
        	        //echo "<h3>" . $k . ":</h3>";
        	        if(isset($tab['layout'])){
        	            $config = array_replace_recursive($config, $tab['layout'] );
        	        }

        	        if(isset($tab['assets'])){
        	            $config = array_replace_recursive($config, $tab['assets'] );
        	        }

        	        if(isset($tab['template_path'])){
        	            $viewResolverPathStack->addPaths($tab['template_path']);
        	        }

        	        if(isset($tab['path']) && isset($config['assetic_configuration']['modules']['frontend'])){
        	            $config['assetic_configuration']['modules']['frontend']['root_path'][] = $tab['path'] . '/assets';
        	        }
        	    }
        	}

        	// Creating the Assetic configuration for images of all available themes
            if (PHP_SAPI !== 'cli') {
        	    $themes = $themeMapper->findAll();
        	    foreach ($themes as $theme) {
        	        $moduleName = 'preview_' . $theme->getArea() . '_' . $theme->getPackage() . '_' . $theme->getTheme();
        	        $config['assetic_configuration']['modules'][$moduleName]['root_path'][] = __DIR__ . '/../../../../../design/'.$theme->getArea().'/'.$theme->getPackage().'/'.$theme->getTheme().'/assets';
        	        $config['assetic_configuration']['modules'][$moduleName]['collections']['admin_images']['assets'] = array('images/screenshots/*.jpg', 'images/screenshots/*.gif', 'images/screenshots/*.png');
        	        $config['assetic_configuration']['modules'][$moduleName]['collections']['admin_images']['options']['output'] = 'theme/';
        	        $config['assetic_configuration']['modules'][$moduleName]['collections']['admin_images']['options']['move_raw'] = 'true';
        	    }
            }

        	//print_r($viewResolverPathStack->getPaths());

        	$e->getApplication()->getServiceManager()->setAllowOverride(true);
        	$e->getApplication()->getServiceManager()->setService('config', $config);
        	//print_r($config);
        	/*foreach($config['core_layout'] as $i=>$t){
        	    echo "<br>". $i . "<br>";
        	    print_r($t);

        	}*/

        	/*foreach($config['assetic_configuration']['modules'] as $i=>$t){
        	    echo "<br>". $i . "<br>";
        	    print_r($t);

        	}*/
        	
        	/*echo '<pre>';
        	print_r($config['assetic_configuration']);
        	echo '</pre>';*/
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
                },

                'libAssetPath' => function($sm) {
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
                'admin_navigation' => 'PlaygroundDesign\Service\AdminNavigationFactory',
                'playgrounddesign_module_options' => function  ($sm) {
                    $config = $sm->get('Configuration');

                    return new Options\ModuleOptions(isset($config['playgrounddesign']) ? $config['playgrounddesign'] : array());
                },
                'playgrounddesign_theme_mapper' => function  ($sm) {

                    return new Mapper\Theme($sm->get('playgrounddesign_doctrine_em'), $sm->get('playgrounddesign_module_options'));
                },

                'playgrounddesign_company_mapper' => function  ($sm) {
                    return new Mapper\Company($sm->get('playgrounddesign_doctrine_em'), $sm->get('playgrounddesign_module_options'));
                },

                'playgrounddesign_theme_form' => function  ($sm) {
                    $translator = $sm->get('translator');
                    $form = new Form\Admin\Theme(null, $sm, $translator);
                    $theme = new Entity\Theme();
                    $form->setInputFilter($theme->getInputFilter());

                    return $form;
                },

                'playgrounddesign_company_form' => function  ($sm) {
                    $translator = $sm->get('translator');
                    $form = new Form\Admin\Company(null, $sm, $translator);
                    $company = new Entity\Company();
                    $form->setInputFilter($company->getInputFilter());
                    return $form;
                }
            ),
            'aliases' => array(
                'playgrounddesign_doctrine_em' => 'doctrine.entitymanager.orm_default'
            ),
            'invokables' => array(
                'playgrounddesign_theme_service' => 'PlaygroundDesign\Service\Theme',
                'playgrounddesign_company_service' => 'PlaygroundDesign\Service\Company'
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
