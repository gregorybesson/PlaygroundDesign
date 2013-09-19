<?php

namespace PlaygroundDesign\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use PlaygroundDesign\Entity\Skin as SkinEntity;
use PlaygroundDesign\Mapper\Skin;

class SkinAdminController extends AbstractActionController
{

  protected $options, $skinMapper, $adminActionService;

    public function listAction()
    {
        $config = $this->getAdminSkinService()->getServiceManager()->get('config');
        $skinMaper = $this->getSkinMapper();

        $skinsActivated = $skinMaper->findBy(array('is_active' => true));
        $skinsNotActivated = $skinMaper->findBy(array('is_active' => false));

        return array('skinsActivated' => $skinsActivated,
                     'skinsNotActivated' => $skinsNotActivated,
                     'base' => $config['applicationDirectory'],
                     'flashMessages' => $this->flashMessenger()->getMessages());
    }

    public function editAction()
    {
        $config = $this->getAdminSkinService()->getServiceManager()->get('config');
        $skinId = $this->getEvent()->getRouteMatch()->getParam('skinId');
        $skin = $this->getSkinMapper()->findById($skinId);

        $form = $this->getServiceLocator()->get('playgrounddesign_skin_form');
        $form->get('submit')->setLabel('Mettre à jour');

        $request = $this->getRequest();

        $form->bind($skin);

        if ($request->isPost()) {
            $data = array_merge(
                    $request->getPost()->toArray(),
                    $request->getFiles()->toArray()
            );

            $skin = $this->getAdminSkinService()->edit($data, $skin, 'playgrounddesign_skin_form');

            if ($skin) {
                $this->flashMessenger()->addMessage('The skin "'.$skin->getTitle().'" was updated');

                return $this->redirect()->toRoute('admin/playgrounddesign_skinadmin');
            } else {
                 $this->flashMessenger()->addMessage('The skin "'.$skin->getTitle().'" was not updated');

                return $this->redirect()->toRoute('admin/playgrounddesign_skinadmin');
            }
        }

        $viewModel = new ViewModel();
        $viewModel->setTemplate('playground-design/skin-admin/skin');

        return $viewModel->setVariables(array('form' => $form,
                                              'base' => $skin->getBasePath($config['applicationDirectory'])));
    }

    public function newAction()
    {
        $config = $this->getAdminSkinService()->getServiceManager()->get('config');
        
        $form = $this->getServiceLocator()->get('playgrounddesign_skin_form');
        $form->get('submit')->setLabel('Créer');
        
        $request = $this->getRequest();
        $skinEntity = new skinEntity;
        
        if ($request->isPost()) {
            $data = array_merge(
                    $request->getPost()->toArray(),
                    $request->getFiles()->toArray()
            );

            $skin = $this->getAdminSkinService()->create($data, 'playgrounddesign_skin_form');
            if ($skin) {
                $this->flashMessenger()->addMessage('The skin "'.$skin->getTitle().'" was created');

                return $this->redirect()->toRoute('admin/playgrounddesign_skinadmin');
            } else {
                 $this->flashMessenger()->addMessage('The skin "'.$skin->getTitle().'" was not created');

                return $this->redirect()->toRoute('admin/playgrounddesign_skinadmin');
            }
            
        }

        $viewModel = new ViewModel();
        $viewModel->setTemplate('playground-design/skin-admin/skin');

        return $viewModel->setVariables(array('form' => $form,
                                              'base' => $skinEntity->getBasePath($config['applicationDirectory'])));

    }

    public function deleteAction()
    {
        $skinId = $this->getEvent()->getRouteMatch()->getParam('skinId');
        $skin = $this->getSkinMapper()->findById($skinId);
        $title = $skin->getTitle();
        $this->getSkinMapper()->remove($skin);
        $this->flashMessenger()->addMessage('The skin "'.$title.'"has been deleted');

        return $this->redirect()->toRoute('admin/playgrounddesign_skinadmin');
    }

    public function activateAction()
    {

        $skinId = $this->getEvent()->getRouteMatch()->getParam('skinId');
        $skin = $this->getSkinMapper()->findById($skinId);
        
        $skinActivated = $this->getSkinMapper()->findBy(array('is_active' => true,
                                                              'type'      => $skin->getType()));
        $skinActivated[0]->setIsActive(false);
        $this->getSkinMapper()->update($skinActivated[0]);

        $skin->setIsActive(true);
        $this->getSkinMapper()->update($skin);
        $this->flashMessenger()->addMessage('The skin "'.$skin->getTitle().'" is activate');

        return $this->redirect()->toRoute('admin/playgrounddesign_skinadmin');
    }

    public function getSkinMapper()
    {
        if (null === $this->skinMapper) {
            $this->skinMapper = $this->getServiceLocator()->get('playgrounddesign_skin_mapper');
        }

        return $this->skinMapper;
    }

    public function getAdminSkinService()
    {
        if (null === $this->adminActionService) {
            $this->adminActionService = $this->getServiceLocator()->get('playgrounddesign_skin_service');
        }

        return $this->adminActionService;
    }
}