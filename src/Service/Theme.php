<?php

namespace PlaygroundDesign\Service;

use PlaygroundDesign\Entity\Theme as ThemeEntity;
use Laminas\Form\Form;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Validator\NotEmpty;
use Laminas\EventManager\EventManagerAwareTrait;
use PlaygroundDesign\Options\ModuleOptions;
use DoctrineModule\Validator\NoObjectExists as NoObjectExistsValidator;
use Laminas\Stdlib\ErrorHandler;
use Laminas\ServiceManager\ServiceLocatorInterface;

class Theme
{
    use EventManagerAwareTrait;

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

    public function __construct(ServiceLocatorInterface $locator)
    {
        $this->serviceManager = $locator;
    }

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

        $form = $this->getServiceManager()->get($formClass);

        $form->bind($theme);
        $theme->setImage('tmp');
        $form->setData($data);

        if (!$form->isValid()) {
            return false;
        }
        if (!$this->checkDirectoryTheme($theme, $data)) {
            mkdir($theme->getBasePath().'/'.$data['area'].'/'.$data['package'].'/'.$data['theme'], 0777, true);
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
                mkdir($path, 0777, true);
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
            $contentAssets = str_replace(array('{{area}}', '{{package}}', '{{theme}}', '{{title}}'), array($data['area'], $data['package'], $data['theme'], $data['title']), $contentAssets);
            file_put_contents($theme->getBasePath().$data['area'].'/'.$data['package'].'/'.$data['theme'].'/'.$file, $contentAssets);
        }
    }

    /**
    * findById : recupere l'entite en fonction de son id
    * @param int $id id du theme
    *
    * @return PlaygroundDesign\Entity\Theme $theme
    */
    public function findById($id)
    {
        return $this->getThemeMapper()->findById($id);
    }

    /**
    * insert : insert en base une entité theme
    * @param PlaygroundDesign\Entity\Theme $entity theme
    *
    * @return PlaygroundDesign\Entity\Theme $theme
    */
    public function insert($entity)
    {
        return $this->getThemeMapper()->insert($entity);
    }

    /**
    * insert : met a jour en base une entité theme
    * @param PlaygroundDesign\Entity\Theme $entity theme
    *
    * @return PlaygroundDesign\Entity\Theme $theme
    */
    public function update($entity)
    {
        return $this->getThemeMapper()->update($entity);
    }

    /**
    * remove : supprimer une entite theme
    * @param PlaygroundDesign\Entity\Theme $entity theme
    *
    */
    public function remove($entity)
    {
        $this->getThemeMapper()->remove($entity);
    }

    /**
    * findActiveTheme : recupere des entites en fonction du filtre active
    * @param boolean $active valeur du champ active
    *
    * @return collection $themes collection de PlaygroundDesign\Entity\Theme
    */
    public function findActiveTheme($active = true)
    {
        return $this->getThemeMapper()->findActiveTheme($active);
    }

    /**
    * findActiveThemeByArea : recupere des entites active en fonction du filtre Area
    * @param string $area area du theme
    * @param boolean $active valeur du champ active
    *
    * @return collection $themes collection de PlaygroundDesign\Entity\Theme
    */
    public function findActiveThemeByArea($area, $active = true)
    {
        return $this->getThemeMapper()->findActiveThemeByArea($area, $active);
    }

    /**
    * findThemeByAreaPackageAndBase : recupere des entites en fonction des filtre Area, Package et Theme
    * @param string $area area du theme
    * @param string $package package du theme
    * @param string $base base du theme
    *
    * @return collection $themes collection de PlaygroundDesign\Entity\Theme
    */
    public function findThemeByAreaPackageAndBase($area, $package, $base)
    {
        return $this->getThemeMapper()->findThemeByAreaPackageAndBase($area, $package, $base);
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
            $this->setOptions($this->getServiceManager()->get('playgrounddesign_module_options'));
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
}
