<?php

namespace PlaygroundDesign\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\EventManager\EventManager;

use PlaygroundDesign\Entity\Theme as ThemeEntity;
use PlaygroundDesign\Mapper\Theme;

class ThemeAdminController extends AbstractActionController
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
        $themeMaper = $this->getThemeMapper();

        $themesActivated = $themeMaper->findBy(array('is_active' => true));
        $themesNotActivated = $themeMaper->findBy(array('is_active' => false));

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
        $automaticTheme = $this->addAutomaticTheme();
        $themeId = $this->getEvent()->getRouteMatch()->getParam('themeId');
        $theme = $this->getThemeMapper()->findById($themeId);

        $form = $this->getServiceLocator()->get('playgrounddesign_theme_form');
        $form->get('submit')->setLabel('Mettre à jour');

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
                 $this->flashMessenger()->addMessage('The theme "'.$theme->getTitle().'" was not updated');

                return $this->redirect()->toRoute('admin/playgrounddesign_themeadmin');
            }
        }

        $viewModel = new ViewModel();
        $viewModel->setTemplate('playground-design/theme-admin/theme');

        return $viewModel->setVariables(array('form'           => $form,
                                              'automaticTheme' => $automaticTheme));
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
        $automaticTheme = $this->addAutomaticTheme();
        $form = $this->getServiceLocator()->get('playgrounddesign_theme_form');
        $form->get('submit')->setLabel('Créer');
        
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
                 $this->flashMessenger()->addMessage('The theme "'.$theme->getTitle().'" was not created');

                return $this->redirect()->toRoute('admin/playgrounddesign_themeadmin');
            }
            
        }

        $viewModel = new ViewModel();
        $viewModel->setTemplate('playground-design/theme-admin/theme');

        return $viewModel->setVariables(array('form'           => $form,
                                              'automaticTheme' => $automaticTheme));

    }

    /**
    * Suppresion d'un theme 
    *
    * @redirect vers la liste des theme
    */
    public function deleteAction()
    {
        $themeId = $this->getEvent()->getRouteMatch()->getParam('themeId');
        $theme = $this->getThemeMapper()->findById($themeId);
        $title = $theme->getTitle();
        $this->getThemeMapper()->remove($theme);
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
        $theme = $this->getThemeMapper()->findById($themeId);
        
        $themeActivated = $this->getThemeMapper()->findBy(array('is_active' => true,
                                                              'type'      => $theme->getType()));
        $themeActivated[0]->setIsActive(false);
        $this->getThemeMapper()->update($themeActivated[0]);

        $theme->setIsActive(true);
        $this->getThemeMapper()->update($theme);
        $this->flashMessenger()->addMessage('The theme "'.$theme->getTitle().'" is activate');

        $eventManager = new EventManager();
        $eventManager->trigger(\Zend\ModuleManager\ModuleEvent::EVENT_MERGE_CONFIG);

        return $this->redirect()->toRoute('admin/playgrounddesign_themeadmin');
    }

    public function addAutomaticTheme()
    {
        
        return 2;
    }

    /**
    * Recuperation du themeMapper
    *
    * @return PlaygroundDesign\Mapper\Theme $themeMapper
    */
    public function getThemeMapper()
    {
        if (null === $this->themeMapper) {
            $this->themeMapper = $this->getServiceLocator()->get('playgrounddesign_theme_mapper');
        }

        return $this->themeMapper;
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