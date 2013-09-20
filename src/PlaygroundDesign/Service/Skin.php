<?php

namespace PlaygroundDesign\Service;

use PlaygroundDesign\Entity\Skin as SkinEntity;

use Zend\Form\Form;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use ZfcBase\EventManager\EventProvider;
use PlaygroundDesign\Options\ModuleOptions;
use DoctrineModule\Validator\NoObjectExists as NoObjectExistsValidator;
use Zend\Stdlib\ErrorHandler;

class Skin extends EventProvider implements ServiceManagerAwareInterface
{

    /**
     * @var skinMapperInterface
     */
    protected $skinMapper;
  
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
     * This service is ready for create a skin
     *
     * @param  array  $data
     * @param  string $formClass
     *
     * @return \PlaygroundPartnership\Entity\Skin
     */
    public function create(array $data, $formClass)
    {
        $skin  = new SkinEntity;
        $entityManager = $this->getServiceManager()->get('playgrounddesign_doctrine_em');

        $form  = $this->getServiceManager()->get($formClass);

        $form->bind($skin);

        $path = $this->getOptions()->getMediaPath() . DIRECTORY_SEPARATOR;
        $media_url = $this->getOptions()->getMediaUrl() . '/';

        $input = $form->getInputFilter()->get('title');
        $noObjectExistsValidator = new NoObjectExistsValidator(array(
                'object_repository' => $entityManager->getRepository('PlaygroundDesign\Entity\Skin'),
                'fields'            => 'title',
                'messages'          => array('objectFound' => 'Ce titre existe déjà !')
        ));

        $input->getValidatorChain()->addValidator($noObjectExistsValidator);
        $skin->setImage('tmp');
        $form->setData($data);

        if (!$form->isValid() && !$this->checkDirectorySkin($skin, $data)) {
            return false;
        }

        $this->createFiles($skin, $data);

        $skinMapper = $this->getSkinMapper();
        $skin = $skinMapper->insert($skin);

        if (!empty($data['uploadImage']['tmp_name'])) {
            ErrorHandler::start();
            move_uploaded_file($data['uploadImage']['tmp_name'], $path . $skin->getId() . "-" . $data['uploadImage']['name']);
            $skin->setImage($media_url . $skin->getId() . "-" . $data['uploadImage']['name']);
            ErrorHandler::stop(true);
        }

        $skin = $skinMapper->update($skin);

        return $skin;
    }

    /**
     *
     * This service is ready for edit a skin
     *
     * @param  array  $data
     * @param  string $skin
     * @param  string $formClass
     *
     * @return \PlaygroundDesignEntity\Skin
     */
    public function edit(array $data, $skin, $formClass)
    {
       $entityManager = $this->getServiceManager()->get('playgrounddesign_doctrine_em');

        $form  = $this->getServiceManager()->get($formClass);

        $form->bind($skin);

        $path = $this->getOptions()->getMediaPath() . DIRECTORY_SEPARATOR;

        $media_url = $this->getOptions()->getMediaUrl() . '/';

        $form->setData($data);
 
        if (!$form->isValid() && !$this->checkDirectorySkin($skin, $data)) {
            return false;
        }

        if (!empty($data['uploadImage']['tmp_name'])) {
            ErrorHandler::start();
            move_uploaded_file($data['uploadImage']['tmp_name'], $path . $skin->getId() . "-" . $data['uploadImage']['name']);
            $skin->setImage($media_url . $skin->getId() . "-" . $data['uploadImage']['name']);
            ErrorHandler::stop(true);
        }

        $skin = $this->getSkinMapper()->update($skin);

        return $skin;
    }
    
    /**
     *
     * Check if the directory skin exist
     *
     * @param  \PlaygroundPartnership\Entity\Skin $skin
     * @param  array  $data
     *
     * @return bool $bool
     */
    public function checkDirectorySkin($skin, $data)
    {
        $newUrlTheme = $skin->getBasePath().'/'.$data['type'].'/'.$data['package'].'/'.$data['theme'];
        if (!is_dir($newUrlTheme)) {
        
            return false;
        }

        return true;
    }

    public function createFiles($skin, $data)
    {
        foreach (self::$files as $file) {
            if (file_exists($skin->getBasePath().'/design/'.$data['type'].'/'.$data['package'].'/'.$data['theme'].'/'.$file)) {
                continue;
            }
            $contentAssets = file_get_contents(__DIR__.'/../Templates/'.$file);
            $contentAssets = str_replace(array('{{type}}', '{{package}}','{{theme}}'), array($data['type'], $data['package'], $data['theme']), $contentAssets);
            file_put_contents($skin->getBasePath().'/design/'.$data['type'].'/'.$data['package'].'/'.$data['theme'].'/'.$file, $contentAssets);
        }
    }

    /**
     * getSkinMapper
     *
     * @return SkinMapperInterface
     */
    public function getSkinMapper()
    {
        if (null === $this->skinMapper) {
            $this->skinMapper = $this->getServiceManager()->get('playgrounddesign_skin_mapper');
        }

        return $this->skinMapper;
    }

    /**
     * setSkinMapper
     * @param  SkinMapperInterface $skinMapper
     *
     * @return PlaygroundPartnership\Entity\Skin Skin
     */
    public function setSkinMapper($skinMapper)
    {
        $this->skinMapper = $skinMapper;

        return $this;
    }

    /**
     * setOptions
     * @param  ModuleOptions $options
     *
     * @return PlaygroundDesign\Service\Skin $this
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

