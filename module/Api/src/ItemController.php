<?php

namespace Api;

use Destiny\Client\ClientProviderTrait;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class ItemController extends AbstractActionController
{
    use ClientProviderTrait;

    public function equipAction()
    {
        if (!$this->identity()) {
            return new JsonModel();
        }

        /** @var \Zend\Http\Request $request */
        $request = $this->getRequest();
        if (!$request->isPost()) {
            return new JsonModel();
        }

        $json = @json_decode($request->getContent(), true);
        if (null === $json) {
            return new JsonModel();
        }

        return new JsonModel($this->getClient()->equipItem($json));
    }

    public function transferAction()
    {
        if (!$this->identity()) {
            return new JsonModel();
        }

        /** @var \Zend\Http\Request $request */
        $request = $this->getRequest();
        if (!$request->isPost()) {
            return new JsonModel();
        }

        $json = @json_decode($request->getContent(), true);
        if (null === $json) {
            return new JsonModel();
        }

        return new JsonModel($this->getClient()->transferItem($json));
    }
}
