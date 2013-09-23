<?php

namespace PlaygroundDesign\Service;

use PlaygroundDesign\Entity\Theme as ThemeEntity;

use Zend\Form\Form;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
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
        $theme  = new ThemeEntity;
        $entityManager = $this->getServiceManager()->get('playgrounddesign_doctrine_em');

        $form  = $this->getServiceManager()->get($formClass);

        $form->bind($theme);

        $path = $this->getOptions()->getMediaPath() . DIRECTORY_SEPARATOR;
        $media_url = $this->getOptions()->getMediaUrl() . '/';

        $input = $form->getInputFilter()->get('title');
        $noObjectExistsValidator = new NoObjectExistsValidator(array(
                'object_repository' => $entityManager->getRepository('PlaygroundDesign\Entity\Theme'),
                'fields'            => 'title',
                'messages'          => array('objectFound' => 'Ce titre existe déjà !')
        ));

        $input->getValidatorChain()->addValidator($noObjectExistsValidator);
        $theme->setImage('tmp');
        $form->setData($data);

        if (!$form->isValid() && !$this->checkDirectoryTheme($theme, $data)) {
            return false;
        }

        $this->createFiles($theme, $data);

        $themeMapper = $this->getThemeMapper();
        $theme = $themeMapper->insert($theme);

        if (!empty($data['uploadImage']['tmp_name'])) {
            ErrorHandler::start();
            move_uploaded_file($data['uploadImage']['tmp_name'], $path . $theme->getId() . "-" . $data['uploadImage']['name']);
            $theme->setImage($media_url . $theme->getId() . "-" . $data['uploadImage']['name']);
            ErrorHandler::stop(true);
        }

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
       $entityManager = $this->getServiceManager()->get('playgrounddesign_doctrine_em');

        $form  = $this->getServiceManager()->get($formClass);

        $form->bind($theme);

        $path = $this->getOptions()->getMediaPath() . DIRECTORY_SEPARATOR;

        $media_url = $this->getOptions()->getMediaUrl() . '/';

        $form->setData($data);
 
        if (!$form->isValid() && !$this->checkDirectoryTheme($theme, $data)) {
            return false;
        }

        if (!empty($data['uploadImage']['tmp_name'])) {
            ErrorHandler::start();
            move_uploaded_file($data['uploadImage']['tmp_name'], $path . $theme->getId() . "-" . $data['uploadImage']['name']);
            $theme->setImage($media_url . $theme->getId() . "-" . $data['uploadImage']['name']);
            ErrorHandler::stop(true);
        }

        $theme = $this->getThemeMapper()->update($theme);

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
        $newUrlTheme = $theme->getBasePath().'/'.$data['type'].'/'.$data['package'].'/'.$data['theme'];
        if (!is_dir($newUrlTheme)) {
        
            return false;
        }

        return true;
    }

    public function createFiles($theme, $data)
    {
        foreach (self::$files as $file) {
            if (file_exists($theme->getBasePath().'/design/'.$data['type'].'/'.$data['package'].'/'.$data['theme'].'/'.$file)) {
                continue;
            }
            $contentAssets = file_get_contents(__DIR__.'/../Templates/'.$file);
            $contentAssets = str_replace(array('{{type}}', '{{package}}','{{theme}}'), array($data['type'], $data['package'], $data['theme']), $contentAssets);
            file_put_contents($theme->getBasePath().'/design/'.$data['type'].'/'.$data['package'].'/'.$data['theme'].'/'.$file, $contentAssets);
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

