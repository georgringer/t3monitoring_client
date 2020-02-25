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
    'version' => '9.1.0',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-10.4.99',
            'reports' => '9.5.0-10.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
