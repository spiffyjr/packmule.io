<?php

namespace Destiny\Client;

use Zend\ServiceManager\ServiceLocatorAwareInterface;

trait ClientProviderTrait
{
    /**
     * @return \Destiny\Client\Client
     */
    public function getClient()
    {
        if (!$this instanceof ServiceLocatorAwareInterface) {
            throw new \RuntimeException('trait must be implemented on a ServiceLocatorAwareInterface');
        }
        return $this->getServiceLocator()->get('destiny.client');
    }
}
