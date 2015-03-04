<?php

$isLocal = isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] == '127.0.0.1';

return [
    'modules' => [
        'AssetManager',
        'Api',
        'Destiny',
        'Application',
    ],
    'module_listener_options' => [
        'module_paths' => [
            './module',
            './vendor',
        ],
        'config_glob_paths' => [
            'config/autoload/{,*.}{global,local}.php',
        ],

        // Whether or not to enable a configuration cache.
        // If enabled, the merged configuration will be cached and used in
        // subsequent requests.
        'config_cache_enabled' => false,

        // The key used to create the configuration cache file name.
        'config_cache_key' => 1,

        // Whether or not to enable a module class map cache.
        // If enabled, creates a module class map cache which will be used
        // by in future requests, to reduce the autoloading process.
        'module_map_cache_enabled' => false,

        // The key used to create the class map cache file name.
        'module_map_cache_key' => 1,

        // The path in which to cache merged configuration.
        'cache_dir' => 'data/cache/zf2',
    ],
];
