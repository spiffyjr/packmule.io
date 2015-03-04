<?php

namespace Api;

use Destiny\Client\ClientProviderTrait;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class VaultController extends AbstractActionController
{
    use ClientProviderTrait;

    public function indexAction()
    {
        $definitions = $this->params()->fromQuery('definitions', 'false');
        $membershipType = $this->identity()->getMembershipType();

        return new JsonModel($this->getClient()->getAccountVault($membershipType, $definitions));
    }
}
