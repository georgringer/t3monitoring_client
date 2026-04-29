<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Client extension for the t3monitoring service',
    'description' => '',
    'category' => 'plugin',
    'author' => 'Georg Ringer',
    'author_email' => '',
    'state' => 'stable',
    'version' => '11.0.1',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-14.3.99',
            'install' => '12.4.0-14.3.99',
            'extensionmanager' => '12.4.0-14.3.99',
            'reports' => '12.4.0-14.3.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
