<?php

$EM_CONF[$_EXTKEY] = array(
    'title' => 'Client extension for the t3monitoring service',
    'description' => '',
    'category' => 'plugin',
    'author' => 'Georg Ringer',
    'author_email' => '',
    'state' => 'alpha',
    'internal' => '',
    'uploadfolder' => false,
    'createDirs' => '',
    'clearCacheOnLoad' => true,
    'version' => '0.0.1',
    'constraints' => array(
        'depends' => array(
            'typo3' => '4.4.0-4.5.99',
        ),
        'conflicts' => array(),
        'suggests' => array(),
    ),
);
