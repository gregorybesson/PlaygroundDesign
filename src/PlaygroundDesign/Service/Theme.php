<?php

namespace PlaygroundDesign\Service;

use PlaygroundDesign\Entity\Theme as ThemeEntity;

use Zend\Form\Form;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\Validator\NotEmpty;
use ZfcBase\EventManager\EventProvider;
use PlaygroundDesign\Options\ModuleOptions;
use DoctrineModule\Validator\NoObjectExists as NoObjectExistsValidator;
use Zend\Stdlib\ErrorHandler;

class Theme extends EventProvider implements ServiceManagerAwareInterface
{

    /**
     * @var themeMapperInterface
     */
    protected $themeMapper;
  
    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var UserServiceOptionsInterface
     */
    protected $options;

    public static $files = array('assets.php', 'layout.php', 'theme.php');

    /**
     *
     * This service is ready for create a theme
     *
     * @param  array  $data
     * @param  string $formClass
     *
     * @return \PlaygroundPartnership\Entity\Theme
     */
    public function create(array $data, $formClass)
    {
        $valid = new NotEmpty();
        if (!$valid->isValid($data['title'])) {
            return false;
        }

        $theme = new ThemeEntity;
        $entityManager = $this->getServiceManager()->get('playgrounddesign_doctrine_em');

        $form = $this->getServiceManager()->get($formClass);

        $form->bind($theme);
        $theme->setImage('tmp');
        $form->setData($data);

        if (!$form->isValid() || !$this->checkDirectoryTheme($theme, $data)) {
            return false;
        }

        $this->createFiles($theme, $data);

        $themeMapper = $this->getThemeMapper();
        $theme = $themeMapper->insert($theme);

        $this->uploadImage($theme, $data);

        $theme = $themeMapper->update($theme);

        return $theme;
    }

    /**
     *
     * This service is ready for edit a theme
     *
     * @param  array  $data
     * @param  string $theme
     * @param  string $formClass
     *
     * @return \PlaygroundDesignEntity\Theme
     */
    public function edit(array $data, $theme, $formClass)
    {
        $valid = new NotEmpty();
        if (!$valid->isValid($data['title'])) {
            return false;
        }

        $entityManager = $this->getServiceManager()->get('playgrounddesign_doctrine_em');

        $form  = $this->getServiceManager()->get($formClass);

        $form->bind($theme);

        $form->setData($data);
 
        if (!$form->isValid() || !$this->checkDirectoryTheme($theme, $data)) {
            return false;
        }

        $this->uploadImage($theme, $data);

        $theme = $this->getThemeMapper()->update($theme);

        return $theme;
    }

    public function uploadImage($theme, $data)
    {
         if (!empty($data['uploadImage']['tmp_name'])) {
            $path = $this->getOptions()->getMediaPath() . $data['area'] . DIRECTORY_SEPARATOR . $data['package'] . DIRECTORY_SEPARATOR . $data['theme'] . '/assets/images/screenshots/';
            if (!is_dir($path)) {
                mkdir($path,0777, true);
            }
            $media_url = $this->getOptions()->getMediaUrl() . '/';
            move_uploaded_file($data['uploadImage']['tmp_name'], $path . $theme->getId() . "-" . $data['uploadImage']['name']);
            $theme->setImage($media_url . $theme->getId() . "-" . $data['uploadImage']['name']);
        }
        return $theme;
    }
    
    /**
     *
     * Check if the directory theme exist
     *
     * @param  \PlaygroundPartnership\Entity\Theme $theme
     * @param  array  $data
     *
     * @return bool $bool
     */
    public function checkDirectoryTheme($theme, $data)
    {
        
        $newUrlTheme = $theme->getBasePath().'/'.$data['area'].'/'.$data['package'].'/'.$data['theme'];
        if (!is_dir($newUrlTheme)) {
        
            return false;
        }

        return true;
    }

    public function createFiles($theme, $data)
    {
        foreach (self::$files as $file) {
            if (file_exists($theme->getBasePath().$data['area'].'/'.$data['package'].'/'.$data['theme'].'/'.$file)) {
                continue;
            }
            $contentAssets = file_get_contents(__DIR__.'/../Templates/'.$file);
            $contentAssets = str_replace(array('{{area}}', '{{package}}','{{theme}}', '{{title}}'), array($data['area'], $data['package'], $data['theme'], $data['title']), $contentAssets);
            file_put_contents($theme->getBasePath().$data['area'].'/'.$data['package'].'/'.$data['theme'].'/'.$file, $contentAssets);
        }
    }

    /**
     * getThemeMapper
     *
     * @return ThemeMapperInterface
     */
    public function getThemeMapper()
    {
        if (null === $this->themeMapper) {
            $this->themeMapper = $this->getServiceManager()->get('playgrounddesign_theme_mapper');
        }

        return $this->themeMapper;
    }

    /**
     * setThemeMapper
     * @param  ThemeMapperInterface $themeMapper
     *
     * @return PlaygroundPartnership\Entity\Theme Theme
     */
    public function setThemeMapper($themeMapper)
    {
        $this->themeMapper = $themeMapper;

        return $this;
    }

    /**
     * setOptions
     * @param  ModuleOptions $options
     *
     * @return PlaygroundDesign\Service\Theme $this
     */
    public function setOptions(ModuleOptions $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * getOptions
     *
     * @return ModuleOptions $optins
     */
    public function getOptions()
    {
        if (!$this->options instanceof ModuleOptions) {
            $this->setOptions($this->getServiceManager()->get('playgrounddesignmodule_options'));
        }

        return $this->options;
    }

    /**
     * Retrieve service manager instance
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Set service manager instance
     *
     * @param  ServiceManager $serviceManager
     * @return User
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;

        return $this;
    }
}  

