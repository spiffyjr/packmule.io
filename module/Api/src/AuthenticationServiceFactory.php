<?php

namespace Api;

use Destiny\Authentication\AuthenticationAdapter;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\ServiceManager;

class AuthenticationServiceFactory
{
    /**
     * @param ServiceManager $services
     * @return AuthenticationService
     */
    public function __invoke(ServiceManager $services)
    {
        return new AuthenticationService(
            $services->get('destiny.authentication_storage'),
            new AuthenticationAdapter($services->get('destiny.client'))
        );
    }
}
