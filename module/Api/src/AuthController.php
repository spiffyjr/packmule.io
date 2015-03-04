<?php

namespace Api;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\View\Model\JsonModel;

class AuthController extends AbstractActionController
{
    public function sessionAction()
    {
        if (!$this->identity()) {
            return new JsonModel();
        }

        $hydrator = new ClassMethods();
        $result = $hydrator->extract($this->getAuthenticationService()->getIdentity());
        unset($result['jar']);

        return new JsonModel($result);
    }

    public function loginAction()
    {
        if ($this->identity()) {
            return new JsonModel(['status' => 'already logged in']);
        }

        /** @var \Zend\Http\Request $request */
        $request = $this->getRequest();

        if (!$request->isPost()) {
            return new JsonModel(['status' => 'post required']);
        }

        $form = new LoginForm();
        $form->setData(json_decode($request->getContent(), true));

        if (!$form->isValid()) {
            return new JsonModel($form->getMessages());
        }

        $data = $form->getData();

        $authService = $this->getAuthenticationService();
        $adapter = $authService->getAdapter();
        $adapter->setIdentity($data['identity']);
        $adapter->setCredential($data['credential']);
        $adapter->setPlatform($data['platform']);

        $result = $authService->authenticate();

        if (!$result->isValid()) {
            return new JsonModel(['status' => $result->getMessages()]);
        }

        return new JsonModel(['status' => 'success']);
    }

    public function logoutAction()
    {
        if (!$this->identity()) {
            return new JsonModel(['status' => 'not logged in']);
        }

        $this->getAuthenticationService()->clearIdentity();
        return new JsonModel(['status' => 'success']);
    }

    /**
     * @return \Zend\Authentication\AuthenticationService
     */
    public function getAuthenticationService()
    {
        return $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
    }
}
