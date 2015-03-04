<?php

namespace Api;

use Destiny\Client\ClientProviderTrait;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class CharacterController extends AbstractActionController
{
    use ClientProviderTrait;

    public function indexAction()
    {
        $membershipType = $this->identity()->getMembershipType();
        $membershipId = $this->identity()->getMembershipId();
        $definitions = $this->params()->fromQuery('definitions', 'false');

        return new JsonModel($this->getClient()->getAccount($membershipType, $membershipId, $definitions));
    }

    public function inventoryAction()
    {
        if (!$this->identity()) {
            throw new \RuntimeException('Invalid login');
        }

        $membershipType = $this->identity()->getMembershipType();
        $membershipId = $this->identity()->getMembershipId();
        $characterId = $this->params('characterId');
        $definitions = $this->params()->fromQuery('definitions', 'false');

        $inventory = $this
            ->getClient()
            ->getCharacterInventory(
                $membershipType,
                $membershipId,
                $characterId,
                $definitions
            );

        return new JsonModel($inventory);
    }
}
