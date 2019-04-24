<?php

namespace PlaygroundDesign\Service;

use PlaygroundDesign\Entity\Settings as SettingsEntity;
use Zend\Form\Form;
use Zend\ServiceManager\ServiceManager;
use Zend\Validator\NotEmpty;
use Zend\EventManager\EventManagerAwareTrait;
use PlaygroundDesign\Options\ModuleOptions;
use DoctrineModule\Validator\NoObjectExists as NoObjectExistsValidator;
use Zend\Stdlib\ErrorHandler;
use Zend\ServiceManager\ServiceLocatorInterface;

class Settings
{
    use EventManagerAwareTrait;

    /**
     * @var settingsMapperInterface
     */
    protected $settingsMapper;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var UserServiceOptionsInterface
     */
    protected $options;

    public function __construct(ServiceLocatorInterface $locator)
    {
        $this->serviceManager = $locator;
    }

    /**
     *
     * This service is ready to create a settings
     *
     * @param  array  $data
     * @param  string $formClass
     *
     * @return \PlaygroundDesign\Entity\Settings
     */
    public function create(array $data)
    {
        $settings = new SettingsEntity;
        $entityManager = $this->getServiceManager()->get('playgrounddesign_doctrine_em');

        $form = $this->getServiceManager()->get('playgrounddesign_settings_form');

        $form->bind($settings);
        $form->setData($data);

        if (!$form->isValid()) {
            return false;
        }

        $settingsMapper = $this->getSettingsMapper();
        $settings = $settingsMapper->insert($settings);

        return $settings;
    }

    /**
     *
     * This service is ready for edit a settings
     *
     * @param  array  $data
     * @param  string $settings
     * @param  string $formClass
     *
     * @return \PlaygroundDesign\Entity\Settings
     */
    public function edit(array $data, $settings)
    {
        
        $entityManager = $this->getServiceManager()->get('playgrounddesign_doctrine_em');

        $form  = $this->getServiceManager()->get('playgrounddesign_settings_form');

        $form->bind($settings);

        $form->setData($data);

        if (!$form->isValid()) {
            return false;
        }
    
        $settings = $this->getSettingsMapper()->update($settings);

        return $settings;
    }

    /**
     * getSettingsMapper
     *
     * @return SettingsMapperInterface
     */
    public function getSettingsMapper()
    {
        if (null === $this->settingsMapper) {
            $this->settingsMapper = $this->getServiceManager()->get('playgrounddesign_settings_mapper');
        }

        return $this->settingsMapper;
    }

    /**
     * setSettingsMapper
     * @param  SettingsMapperInterface $settingsMapper
     *
     * @return PlaygroundDesign\Entity\Settings Settings
     */
    public function setSettingsMapper($settingsMapper)
    {
        $this->settingsMapper = $settingsMapper;

        return $this;
    }

    /**
     * setOptions
     * @param  ModuleOptions $options
     *
     * @return PlaygroundDesign\Service\Settings $this
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
