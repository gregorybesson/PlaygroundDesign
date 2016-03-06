<?php

namespace PlaygroundDesign\Controller\Admin;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\EventManager\EventManager;

use PlaygroundDesign\Entity\Theme as ThemeEntity;
use PlaygroundDesign\Mapper\Theme;
use Zend\ServiceManager\ServiceLocatorInterface;

class ThemeController extends AbstractActionController
{
    /**
    * @var $themeMapper mapper de l'entity theme
    */
    protected $themeMapper;

    /**
    * @var $adminActionService Service de l'entity theme
    */
    protected $adminActionService;

    /**
     *
     * @var ServiceManager
     */
    protected $serviceLocator;

    public function __construct(ServiceLocatorInterface $locator)
    {
        $this->serviceLocator = $locator;
    }

    public function getServiceLocator()
    {
        
        return $this->serviceLocator;
    }

    /**
    * Liste des themes
    *
    * @return array $array Passage des variables dans le template
    * themesActivated : theme qui sont activés
    * themesNotActivated : theme qui ne sont pas activés
    * flashMessages : flashMessages
    */
    public function listAction()
    {
        $automaticTheme = $this->addAutomaticTheme();

        $themeMaper = $this->getAdminThemeService();

        $themesActivated = $themeMaper->findActiveTheme();
        $themesNotActivated = $themeMaper->findActiveTheme(false);

        return array('themesActivated'    => $themesActivated,
                     'themesNotActivated' => $themesNotActivated,
                     'automaticTheme'     => $automaticTheme,
                     'flashMessages'      => $this->flashMessenger()->getMessages());
    }

    /**
    * Edition d'un theme
    *
    * @return array $array Passage des variables dans le template
    * form : formulaire qui correspond au theme
    * base : dossier qui correspond a la base du projet
    */
    public function editAction()
    {
        $themeId = $this->getEvent()->getRouteMatch()->getParam('themeId');
        $theme = $this->getAdminThemeService()->findById($themeId);

        $form = $this->getServiceLocator()->get('playgrounddesign_theme_form');

        $request = $this->getRequest();

        $form->bind($theme);

        if ($request->isPost()) {
            $data = array_merge(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );

            $theme = $this->getAdminThemeService()->edit($data, $theme, 'playgrounddesign_theme_form');

            if ($theme) {
                $this->flashMessenger()->addMessage('The theme "'.$theme->getTitle().'" was updated');

                return $this->redirect()->toRoute('admin/playgrounddesign_themeadmin');
            } else {
                 $this->flashMessenger()->addMessage('The theme was not updated');

                return $this->redirect()->toRoute('admin/playgrounddesign_themeadmin');
            }
        }

        $viewModel = new ViewModel();
        $viewModel->setTemplate('playground-design/theme-admin/theme');

        return $viewModel->setVariables(array('form'           => $form,
                                              'automaticTheme' => 0));
    }

    /**
    * Creation d'un theme
    *
    * @return array $array Passage des variables dans le template
    * form : formulaire qui correspond au theme
    * base : dossier qui correspond a la base du projet
    */
    public function newAction()
    {
        $form = $this->getServiceLocator()->get('playgrounddesign_theme_form');

        $request = $this->getRequest();
        $themeEntity = new themeEntity;

        if ($request->isPost()) {
            $data = array_merge(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );

            $theme = $this->getAdminThemeService()->create($data, 'playgrounddesign_theme_form');
            if ($theme) {
                $this->flashMessenger()->addMessage('The theme "'.$theme->getTitle().'" was created');

                return $this->redirect()->toRoute('admin/playgrounddesign_themeadmin');
            } else {
                 $this->flashMessenger()->addMessage('The theme was not created');

                return $this->redirect()->toRoute('admin/playgrounddesign_themeadmin');
            }

        }

        $viewModel = new ViewModel();
        $viewModel->setTemplate('playground-design/theme-admin/theme');

        return $viewModel->setVariables(array('form'           => $form,
                                              'automaticTheme' => 0));

    }

    /**
    * Suppresion d'un theme
    *
    * @redirect vers la liste des theme
    */
    public function deleteAction()
    {
        $themeId = $this->getEvent()->getRouteMatch()->getParam('themeId');
        $theme = $this->getAdminThemeService()->findById($themeId);
        $title = $theme->getTitle();
        $this->getAdminThemeService()->remove($theme);
        $this->flashMessenger()->addMessage('The theme "'.$title.'"has been deleted');

        return $this->redirect()->toRoute('admin/playgrounddesign_themeadmin');
    }

    /**
    * Activation d'un theme
    *
    * @redirect vers la liste des theme
    */
    public function activateAction()
    {

        $themeId = $this->getEvent()->getRouteMatch()->getParam('themeId');
        $theme = $this->getAdminThemeService()->findById($themeId);

        $themeActivated = $this->getAdminThemeService()->findActiveThemeByArea($theme->getArea());
        if (sizeof($themeActivated) > 0) {
            $themeActivated[0]->setIsActive(false);
            $this->getAdminThemeService()->update($themeActivated[0]);
        }

        $theme->setIsActive(true);
        $this->getAdminThemeService()->update($theme);
        $this->flashMessenger()->addMessage('The theme "'.$theme->getTitle().'" is activate');

        $eventManager = new EventManager();
        $eventManager->trigger(\Zend\ModuleManager\ModuleEvent::EVENT_MERGE_CONFIG);

        return $this->redirect()->toRoute('admin/playgrounddesign_themeadmin');
    }


    /**
    * Permet de rajouter automatique des thèmes
    *
    *
    * @eturn int $nbTheme
    */
    public function addAutomaticTheme()
    {
        $nbTheme = 0;
        $theme = new ThemeEntity();

        $directoryThemes = $theme->getBasePath();

        $nbTheme = $this->checkAssets($directoryThemes, $nbTheme);

        return $nbTheme;
    }

    /**
    * checkAssets : Permet de creer automatiquement un theme depuis le filer
    * @param $directoryThemes : dossier dans lequel chercher un assets.php
    * @param $nbTheme : nombre de theme ajouté automatiquement
    *
    * Fonction recursive qui parcourt tous le dossier theme
    *
    * @return $nbTheme nombre de theme ajouté automatiquement
    */
    public function checkAssets($directoryThemes, $nbTheme)
    {
        $files = scandir($directoryThemes);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                if (is_dir($directoryThemes.'/'.$file)) {
                    $nbTheme = $this->checkAssets($directoryThemes.'/'.$file, $nbTheme);
                } else {
                    if ($file=="theme.php") {
                        $themeDefine = explode('//', $directoryThemes.'/'.$file);
                        $themeDefine = explode('/', $themeDefine[1]);
                        $area = $themeDefine[0];
                        
                        $themeArray = include($directoryThemes.'/theme.php');
                        $package = $themeArray['design']['package']['code'];
                        $title = $themeArray['design']['package']['theme']['title'];
                        $themeCode = $themeArray['design']['package']['theme']['code'];

                        $themes = $this->getAdminThemeService()->findThemeByAreaPackageAndBase($area, $package, $themeCode);
                        if (sizeof($themes) == 0) {
                            $theme = new ThemeEntity();
                            $theme->setTitle($title);
                            $theme->setArea($area);
                            $theme->setPackage($package);
                            $theme->setTheme($themeCode);
                            $this->getAdminThemeService()->insert($theme);
                            $nbTheme ++;
                        }
                    }
                }

            }
        }

        return $nbTheme;
    }

    /**
    * Recuperation du themeMapper
    *
    * @return PlaygroundDesign\Service\Theme $themeMapper
    */
    public function getAdminThemeService()
    {
        if (null === $this->adminActionService) {
            $this->adminActionService = $this->getServiceLocator()->get('playgrounddesign_theme_service');
        }

        return $this->adminActionService;
    }
}
