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
        $eventManager->attach(\Zend\ModuleManager\ModuleEvent::EVENT_MERGE_CONFIG, array($this, 'onMergeConfig'));
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
            
            if(!empty($themeActivated)) {
                $config['design']['admin']['package'] = $themeActivated['package'];
                $config['design']['admin']['theme'] = $themeActivated['theme'];
            }
            
            // ********************************************
            // Check if a frontend theme is set in database
            // ********************************************
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
            if(!empty($themeActivated)) {
                $config['design']['frontend']['package'] = $themeActivated['package'];
                $config['design']['frontend']['theme'] = $themeActivated['theme'];
            }
        }
        
        // **************************************************
        // Design management : template and assets management
        // **************************************************
        if(isset($config['design'])){
            $viewResolverPathStack = $config['view_manager']['template_path_stack'];
            
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
                                $parentThemeId = $parentTheme[0] .'_'. $parentTheme[1];
        
                                if ($parentThemeId  == $themeId) {
                                    $hasParent = false;
                                }
        
                                $hasParent = true;
                            }
                        }
                    } else {
        
                        $stack = array();
                        foreach($viewResolverPathStack as $path){
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
                foreach($viewResolverPathStack as $path){
                    if(!$result = preg_match('/\/admin\/$/',$path,$matches)){
                        $stack[] = $path;
                    }
                }

                $viewResolverPathStack = array_reverse($stack);
                
                // removing default assetic configuration
                if(isset($config['assetic_configuration']['modules']['admin'])){
                    unset($config['assetic_configuration']['modules']['admin']);
                    unset($config['assetic_configuration']['routes']['admin.*']);
                }
        
                // removing default layout configuration
                if(isset($config['core_layout']['admin'])){
                    unset($config['core_layout']['admin']);
                }
                
                //clearing the template_path_stack
                $config['view_manager']['template_path_stack'] = $viewResolverPathStack;
        
                //I then recreate the config
                foreach($themeHierarchy as $theme=>$tab){
                    if(isset($tab['layout'])){
                        $config = array_replace_recursive($config, $tab['layout'] );
                    }
        
                    if(isset($tab['assets'])){
                        $config = array_replace_recursive($config, $tab['assets'] );
                    }
        
                    if(isset($tab['template_path'])){
                        $config['view_manager']['template_path_stack'] = array_merge($config['view_manager']['template_path_stack'],$tab['template_path']);
                    }
        
                    if(isset($tab['path']) && isset($config['assetic_configuration']['modules']['admin'])){
                        $config['assetic_configuration']['modules']['admin']['root_path'][] = $tab['path'] . '/assets';
                    }
                }
            }
        
            if(isset($config['design']['frontend']['theme'])){
        
                $viewResolverPathStack = array_reverse($config['view_manager']['template_path_stack']);
                $themeHierarchy = array();
                $hasParent = true;
                $parentTheme = array($config['design']['frontend']['package'], $config['design']['frontend']['theme']);
        
                while($hasParent){

                    // I get the Theme definition file and apply a check on the parent theme.
                    // TODO : Apply recursion to this stuff.
                    $hasParent = false;
                    $frontendPath = __DIR__ . '/../../../../../design/frontend/'. $parentTheme[0] .'/'. $parentTheme[1];
                    $themeId = $parentTheme[0] .'_'. $parentTheme[1];
        
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
                                $parentThemeId = $parentTheme[0] .'_'. $parentTheme[1];
        
                                if ($parentThemeId  == $themeId) {
                                    $hasParent = false;
                                }else{
                                    $hasParent = true;
                                }
                            }
                            
                            // Are there games with a specific URL ?
                            if (isset($configTheme['design']['package']['custom_games'])){
                                foreach($configTheme['design']['package']['custom_games'] as $k=>$v){
                                    $themeHierarchy[$themeId]['custom_games'][$k] = $v;
                                }
                            }
                        }
                    } else {

                        $stack = array();
                        foreach($viewResolverPathStack as $path){
                            if($result = preg_match('/\/frontend\/$/',$path,$matches)){
                                $stack[] = $path;
                            }
                        }
        
                        $themeHierarchy[$themeId]['template_path']= array_reverse($stack);

                        if(isset($config['assetic_configuration']['modules']['frontend'])){
                            $asseticConfig = array('assetic_configuration' => array(
                                'modules' => array('frontend' => $config['assetic_configuration']['modules']['frontend']),
                                'routes' => array('frontend.*' => $config['assetic_configuration']['routes']['frontend.*'])
                            ));
        
                            $themeHierarchy[$themeId]['assets'] = $asseticConfig;
        
                        }
        
                        if(isset($config['core_layout']['frontend'])){
                            $themeHierarchy[$themeId]['layout'] = $config['core_layout']['frontend'];
                        }
                    }
                }
        
                $themeHierarchy = array_reverse($themeHierarchy);
                
                // We remove the former config
                $stack = array();
                foreach($viewResolverPathStack as $path){
                    if(!$result = preg_match('/\/frontend\/$/',$path,$matches)){
                        $stack[] = $path;
                    }
                }
        
                $viewResolverPathStack = array();
        
                $viewResolverPathStack = array_reverse($stack);
        
                // removing default assetic configuration
                if(isset($config['assetic_configuration']['modules']['frontend'])){
                    unset($config['assetic_configuration']['modules']['frontend']);
                    unset($config['assetic_configuration']['routes']['frontend.*']);
                }
        
                // removing default layout configuration
                if(isset($config['core_layout']['frontend'])){
                    unset($config['core_layout']['frontend']);
                }
        
                //clearing the template_path_stack
                $config['view_manager']['template_path_stack'] = $viewResolverPathStack;
                
                //I then recreate the config
                foreach($themeHierarchy as $theme=>$tab){
                    if(isset($tab['layout'])){
                        $config = array_replace_recursive($config, $tab['layout'] );
                    }
        
                    if(isset($tab['assets'])){
                        $config = array_replace_recursive($config, $tab['assets'] );
                    }
        
                    if(isset($tab['template_path'])){
                        //print_r($tab['template_path']);
                        $config['view_manager']['template_path_stack'] = array_merge($config['view_manager']['template_path_stack'], $tab['template_path']);
                    }
        
                    if(isset($tab['path']) && isset($config['assetic_configuration']['modules']['frontend'])){
                        $config['assetic_configuration']['modules']['frontend']['root_path'][] = $tab['path'] . '/assets';
                    }
                    
                    if(isset($tab['custom_games'])){
                        foreach($tab['custom_games'] as $k=>$v){
                            // I take the url model of the game type
                            $routeModel = $config['router']['routes']['frontend']['child_routes'][$v['classType']];
                            
                            // Changing the root of the route
                            $routeModel['options']['route'] = '/';
                            
                            // and removing the trailing slash for each subsequent route
                            foreach($routeModel['child_routes'] as $id=>$ar){
                                $routeModel['child_routes'][$id]['options']['route'] = ltrim($ar['options']['route'], '/');
                            }
                            
                            // then create the hostname route + appending the model updated
                            $config['router']['routes']['frontend.'.$v['url']] = array(
                                'type' => 'Zend\Mvc\Router\Http\Hostname',
                                'options' => array(
                                    'route' => $v['url'],
                                    'defaults' => array(
                                        'id' => $k,
                                        'channel'=> 'embed'
                                    )
                                ),
                                'may_terminate' => true
                            );
                            $config['router']['routes']['frontend.'.$v['url']]['child_routes'][$v['classType']] = $routeModel;
                            
                            $coreLayoutModel = $config['core_layout']['frontend'];
                            $config['core_layout']['frontend.'.$v['url']] = $coreLayoutModel;
                        }
                    }
                }
            }
            
            // Creating the Assetic configuration for images of all available themes
            /*if (PHP_SAPI !== 'cli') {
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

        // positionnement de la langue pour les traductions de date avec strftime
        setlocale(LC_TIME, "fr_FR", 'fr_FR.utf8', 'fra');

        AbstractValidator::setDefaultTranslator($translator,'playgrounddesign');
        
        // Start the session container
        $config = $e->getApplication()->getServiceManager()->get('config');

        $themeMapper = $this->getThemeMapper($serviceManager);

        /*
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
                    $config['design']['frontend']['package'] = $themeActivated->getPackage();
                    $config['design']['frontend']['theme'] = $themeActivated->getTheme();
                }
            }
        }
*/

/*
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
                                $parentThemeId = $parentTheme[0] .'_'. $parentTheme[1];

                                if ($parentThemeId  == $themeId) {
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
                                $parentThemeId = $parentTheme[0] .'_'. $parentTheme[1];

                                if ($parentThemeId  == $themeId) {
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



                        if(isset($config['assetic_configuration']['modules']['frontend'])){
                            $asseticConfig = array('assetic_configuration' => array(
                                'modules' => array('frontend' => $config['assetic_configuration']['modules']['frontend']),
                                'routes' => array('frontend.*' => $config['assetic_configuration']['routes']['frontend.*'])
                            ));

                            $themeHierarchy[$themeId]['assets'] = $asseticConfig;

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
                    //echo "<h3>" . $theme . ":</h3>";
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


            $e->getApplication()->getServiceManager()->setAllowOverride(true);
            $e->getApplication()->getServiceManager()->setService('config', $config);
        }
*/
        
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
                $controllerName  = $match->getParam('controller', 'not-found');
                $actionName      = $match->getParam('action', 'not-found');
                $channel         = $match->getParam('channel', 'not-found');
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
                echo '$channel : ' .$channel. '<br/>'; 
               
*/                
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
        
        // I put channel and area to each view
        $e->getApplication()->getEventManager()->attach(\Zend\Mvc\MvcEvent::EVENT_RENDER, function (\Zend\Mvc\MvcEvent $e) use ($serviceManager) {
        
            $viewModel          = $e->getViewModel();
            $match              = $e->getRouteMatch();
            $channel            = isset($match)? $match->getParam('channel', ''):'';
            $area               = isset($match)? $match->getParam('area', ''):'';
            
            $viewModel->channel = $channel;
            $viewModel->area    = $area;
            
            foreach($viewModel->getChildren() as $child){
                $child->channel = $channel;
                $child->area = $area;
            }
        });
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
                'admin_navigation' => 'PlaygroundDesign\Service\AdminNavigationFactory',
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
