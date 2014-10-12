<?php
namespace PlaygroundDesign\Assetic;

use Assetic\Asset\AssetCollection;
use Assetic\AssetManager;
use Assetic\FilterManager as AsseticFilterManager;
use Assetic\Factory;
use Assetic\Factory\Worker\WorkerInterface;
use Assetic\AssetWriter;
use Assetic\Asset\AssetInterface;
use Assetic\Asset\AssetCache;
use Assetic\Cache\FilesystemCache;
use Zend\View\Renderer\RendererInterface as Renderer;
use AsseticBundle\View\StrategyInterface;
use PlaygroundDesign\Assetic\Configuration;

class Service extends \AsseticBundle\Service
{
    
    /**
     * @var array of route params
     */
    protected $params = array();

    public function __construct(Configuration $configuration)
    {
        parent::__construct($configuration);
    }
    
    /**
     * @param array $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }
    
    /**
     * @return string
     */
    public function getParams()
    {
        return $this->params;
    }
    
    /**
     * Adding route Params to specialize assets on params like channel or game id
     * @see \AsseticBundle\Service::getRouterConfig()
     */
    public function getRouterConfig()
    {
        $assetOptions = $this->configuration->getRoute($this->getRouteName(), $this->getParams());
        return $assetOptions ? $assetOptions : array();
    }

    /**
     * Build collection of assets.
     * TODO : Improve the performance of this highly used function
     */
    public function build()
    {
        $moduleConfiguration = $this->configuration->getModules();
        // I need to reverse the modules so that the last added becomes the first played
        $moduleConfiguration = array_reverse($moduleConfiguration);
        foreach ($moduleConfiguration as $k=>$configuration) {
            //echo "<br/><h3>" . $k . "</h3><br/>";
            //print_r($configuration);
            //echo "<br/>---------" . $k . "-----------<br/>";
            $collections = (array)$configuration['collections'];
            foreach ($collections as $name => $options) {
                //echo "<br/>" . $name . "<br/>";
                $assets = isset($options['assets']) ? $options['assets'] : array();
                //print_r($assets);
                // Je recrée les paths des assets en fonction du tableau de root_path
                // fichiers relatifs quand copiés dans design
                if(is_array($configuration['root_path'])){
                    $paths = array_reverse($configuration['root_path']);
                    //print_r($paths);
                    //$paths = $configuration['root_path'];
                    //$assets = array_unique($assets);
                    //echo "<br/> paths :<br/>";
                    //print_r($paths);
                    foreach($assets as $l=>$asset){
                        //echo "<br/>titre : " . $l . " : <br/>Asset : " . $asset . "<br/>";
                        // Test on assets to remove
                        if(empty($asset)){
                            //echo "<br/> Suppression : " . $l . "<br/>";

                            unset($assets[$l]);
                            unset($options['assets'][$l]);
                            unset($collections[$name]['assets'][$l]);
                            unset($configuration['collections'][$name]['assets'][$l]);
                        } else {
                            if ($this->isRelativePath($asset)){
                                foreach($paths as $path){
                                    //print_r($asset);
                                    //echo "<br/>";
                                    if(is_file($path . '/' . $asset)){
                                        $options['assets'][$l] = $path . '/' . $asset;
                                        $collections[$name]['assets'][$l] = $path . '/' . $asset;
                                        $configuration['collections'][$name]['assets'][$l] = $path . '/' . $asset;
                                        //echo "<br/>trouve :" . $path . '/' . $asset . " <br/>";
                                        break;
                                    }
                                }
                            }
                        }
                        //print_r($asset);
                        //echo "<br/>";
                    }

                    // TODO : used when ?
                    //if(isset($options['options'])){
                        $options['options']['root'] = $configuration['root_path'];
                    //}

                    //echo "<br/>" . $name . "<br>";
                    //print_r($options);
                }
                //print_r($assets);

            }

            $factory = $this->createAssetFactory($configuration);
            foreach ($collections as $name => $options) {
                //echo "<br/>CONFIGURATION OPTIONS<br/>";
                //print_r($options);
                $this->prepareCollection($options, $name, $factory);
            }

        }
    }

    /**
     * @param array $configuration
     * @return Factory\AssetFactory
     * Adding the array test for root_path
     */
    public function createAssetFactory(array $configuration)
    {
        if(is_array($configuration['root_path'])){
            $factory = new Factory\AssetFactory($configuration['root_path'][0]);
        }else{
            $factory = new Factory\AssetFactory($configuration['root_path']);
        }

        $factory->setAssetManager($this->getAssetManager());
        $factory->setFilterManager($this->getFilterManager());
        // Cache buster should be add only if cache is enabled and if is available.
        if ($this->configuration->getCacheEnabled()) {
            $worker = $this->getCacheBusterStrategy();
            if ($worker instanceof WorkerInterface) {
                $factory->addWorker($worker);
            }
        }
        $factory->setDebug($this->configuration->isDebug());
        return $factory;
    }

    /**
     * @param AssetCollection $asset
     * @return string
     * Adding the possibility to give an output path
     * PR to wilmogrod as improvement
     */
    public function moveRaw(AssetCollection $asset, $output=null)
    {
        if (!empty($output) && substr($output, -1) !== '/'){
            $output = $output.'/';
        }
        foreach ($asset as $value) {
            /** @var $value AssetInterface */
            $value->setTargetPath($output. $value->getSourcePath());
            $value = $this->cacheAsset($value);
            $this->writeAsset($value);
        }
    }

    /**
     * @param array $options
     * @param string $name
     * @param Factory\AssetFactory $factory
     * @return void
     * Adding the output var in moveRaw
     * PR to wilmogrod as improvement
     */
    public function prepareCollection($options, $name, Factory\AssetFactory $factory)
    {
        $assets = isset($options['assets']) ? $options['assets'] : array();
        $filters = isset($options['filters']) ? $options['filters'] : array();
        $options = isset($options['options']) ? $options['options'] : array();
        $options['output'] = isset($options['output']) ? $options['output'] : $name;
        $moveRaw = isset($options['move_raw']) && $options['move_raw'];

        $filters = $this->initFilters($filters);

        $asset = $factory->createAsset($assets, $filters, $options);

        // Allow to move all files 1:1 to new directory
        // its particularly useful when this assets are i.e. images.
        if ($moveRaw) {
            // Allow usage of $output to be able to copy the files in different subdirectories
            $this->moveRaw($asset, ($options['output']===$name)?null:$options['output']);
        } else {
            $asset = $this->cacheAsset($asset);
            $this->assetManager->set($name, $asset);
        }
    }
    /**
     *
     * @param string $path
     * @return boolean
     * Adding this function to determine if path is relative or not
     */
    protected function isRelativePath($path)
    {
        if ('@' == $path[0]) {
            return false;
        }

        if (false !== strpos($path, '://') || 0 === strpos($path, '//')) {
            return false;
        }

        if (false !== strpos($path, '*')) {
            return false;
        }
        $absolute = '/' == $path[0] || '\\' == $path[0] || (3 < strlen($path) && ctype_alpha($path[0]) && $path[1] == ':' && ('\\' == $path[2] || '/' == $path[2]));

        return !$absolute;
    }

    /**
     * TODO : PR to wilmogrod to make this function protected instead of private
     */
    private function cacheAsset(AssetInterface $asset)
    {
        return $this->configuration->getCacheEnabled()
        ? new AssetCache($asset, new FilesystemCache($this->configuration->getCachePath()))
        : $asset;
    }

    /**
     * TODO : PR to wilmogrod to make this function protected instead of private
     */
    private function initFilters(array $filters)
    {
        $result = array();

        $fm = $this->getFilterManager();

        foreach ($filters as $alias => $options) {
            $option = null;
            if (is_array($options)) {
                if (!isset($options['name'])) {
                    throw new Exception\InvalidArgumentException(
                            'Filter "' . $alias . '" required option "name"'
                    );
                }

                $name = $options['name'];
                $option = isset($options['option']) ? $options['option'] : null;
            } elseif (is_string($options)) {
                $name = $options;
                unset($options);
            }

            if (is_numeric($alias)) {
                $alias = $name;
            }

            // Filter Id should have optional filter indicator "?"
            $filterId = ltrim($alias, '?');

            if (!$fm->has($filterId)) {
                if (is_array($option) && !empty($option)) {
                    $r = new \ReflectionClass($name);
                    $filter = $r->newInstanceArgs($option);
                } elseif ($option) {
                    $filter = new $name($option);
                } else {
                    $filter = new $name();
                }

                $fm->set($filterId, $filter);
            }

            $result[] = $alias;
        }

        return $result;
    }
}
