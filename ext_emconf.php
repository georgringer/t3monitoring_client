<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Client extension for the t3monitoring service',
    'description' => '',
    'category' => 'plugin',
    'author' => 'Georg Ringer',
    'author_email' => '',
    'state' => 'stable',
    'uploadfolder' => false,
    'createDirs' => '',
    'version' => '9.2.1',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-11.9.99',
            'reports' => '9.5.0-11.9.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
