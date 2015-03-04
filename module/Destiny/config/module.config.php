<?php

return [
    'service_manager' => [
        'factories' => [
            'destiny.authentication_storage' => 'Destiny\Authentication\AuthenticationStorageFactory',
            'destiny.client' => 'Destiny\Client\ClientFactory',
            'destiny.guzzle' => 'Destiny\GuzzleFactory',
        ],
        'invokables' => [],
        'shared' => [
            'destiny.guzzle' => false
        ]
    ],
];
