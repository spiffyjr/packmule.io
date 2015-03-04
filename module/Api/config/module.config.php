<?php

return [
    'router' => [
        'routes' => [
            'api.auth.login' => [
                'type' => 'literal',
                'options' => [
                    'route' => '/api/auth/login',
                    'defaults' => [
                        'controller' => 'api.auth',
                        'action' => 'login'
                    ]
                ]
            ],
            'api.auth.logout' => [
                'type' => 'literal',
                'options' => [
                    'route' => '/api/auth/logout',
                    'defaults' => [
                        'controller' => 'api.auth',
                        'action' => 'logout'
                    ]
                ]
            ],
            'api.auth.session' => [
                'type' => 'literal',
                'options' => [
                    'route' => '/api/auth/session',
                    'defaults' => [
                        'controller' => 'api.auth',
                        'action' => 'session'
                    ]
                ]
            ],
            'api.item.equip' => [
                'type' => 'literal',
                'options' => [
                    'route' => '/api/items/equip',
                    'defaults' => [
                        'controller' => 'api.item',
                        'action' => 'equip'
                    ]
                ]
            ],
            'api.item.transfer' => [
                'type' => 'literal',
                'options' => [
                    'route' => '/api/items/transfer',
                    'defaults' => [
                        'controller' => 'api.item',
                        'action' => 'transfer'
                    ]
                ]
            ],
            'api.characters' => [
                'type' => 'literal',
                'options' => [
                    'route' => '/api/characters',
                    'defaults' => [
                        'controller' => 'api.character',
                        'action' => 'index'
                    ]
                ]
            ],
            'api.characters.inventory' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/api/characters/:characterId/inventory',
                    'defaults' => [
                        'controller' => 'api.character',
                        'action' => 'inventory'
                    ]
                ]
            ],
            'api.vault' => [
                'type' => 'literal',
                'options' => [
                    'route' => '/api/vault',
                    'defaults' => [
                        'controller' => 'api.vault',
                        'action' => 'index'
                    ]
                ]
            ],
        ]
    ],
    'controllers' => [
        'invokables' => [
            'api.auth' => 'Api\AuthController',
            'api.character' => 'Api\CharacterController',
            'api.item' => 'Api\ItemController',
            'api.vault' => 'Api\VaultController'
        ],
    ],
    'service_manager' => [
        'factories' => [
            'api.account_mapper' => 'Api\Mapper\AccountMapperFactory',
            'Zend\Authentication\AuthenticationService' => 'Api\AuthenticationServiceFactory',
        ],
    ],
    'view_manager' => [
        'strategies' => [
            'ViewJsonStrategy',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
