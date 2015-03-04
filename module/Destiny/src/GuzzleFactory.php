<?php

namespace Destiny;

use GuzzleHttp\Client;
use Zend\ServiceManager\ServiceManager;

class GuzzleFactory
{
    /**
     * @param ServiceManager $services
     * @return \Redis
     */
    public function __invoke(ServiceManager $services)
    {
        $guzzle = new Client(['base_url' => 'https://www.bungie.net/']);
        $guzzle->setDefaultOption('verify', false);

        return $guzzle;
    }
}
