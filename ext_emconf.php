<?php

$EM_CONF[$_EXTKEY] = array(
    'title' => 'Client extension for the t3monitoring service',
    'description' => '',
    'category' => 'plugin',
    'author' => 'Georg Ringer',
    'author_email' => '',
    'state' => 'beta',
    'internal' => '',
    'uploadfolder' => false,
    'createDirs' => '',
    'clearCacheOnLoad' => true,
    'version' => '1.0.2',
    'constraints' => array(
        'depends' => array(
            'typo3' => '6.2.0-9.3.99',
            'reports' => '6.2.0-9.3.99',
        ),
        'conflicts' => array(),
        'suggests' => array(),
    ),
);
