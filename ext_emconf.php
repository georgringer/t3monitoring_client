<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Client extension for the t3monitoring service',
    'description' => '',
    'category' => 'plugin',
    'author' => 'Georg Ringer',
    'author_email' => '',
    'state' => 'stable',
    'version' => '10.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '11.3.0-12.4.99',
            'reports' => '11.3.0-12.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
