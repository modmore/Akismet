<?php

return [
    'assets_path' => [
        'area' => 'paths',
        'value' => '{assets_path}components/akismet/',
    ],
    'assets_url' => [
        'area' => 'paths',
        'value' => '{assets_url}components/akismet/',
    ],
    'core_path' => [
        'area' => 'paths',
        'value' => '{core_path}components/akismet/',
    ],
    'api_key' => [
        'area' => 'authentication',
        'value' => '',
    ],
    'cleanup_days_old' => [
        'area' => 'configuration',
        'value' => '30',
    ],
    'debug' => [
        'area' => 'configuration',
        'value' => false,
    ],
    'total_spam' => [
        'area' => 'stats',
        'value' => 0,
    ],
    'total_ham' => [
        'area' => 'stats',
        'value' => 0,
    ],
];