<?php

namespace Destiny\Authentication;

use Zend\Authentication\Storage\Session;

class AuthenticationStorageFactory
{
    public function __invoke()
    {
        return new Session('destiny_authentication');
    }
}
