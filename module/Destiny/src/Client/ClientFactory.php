<?php

namespace Destiny\Client;

use Zend\ServiceManager\ServiceManager;

class ClientFactory
{
    /**
     * @param ServiceManager $services
     * @return \Redis
     */
    public function __invoke(ServiceManager $services)
    {
        /** @var \Zend\Authentication\Storage\Session $storage */
        $storage = $services->get('destiny.authentication_storage');
        $client = new Client($services->get('destiny.guzzle'));

        if (!$storage->isEmpty()) {
            $client->setJar($storage->read()->getJar());
        }

        return $client;
    }
}
