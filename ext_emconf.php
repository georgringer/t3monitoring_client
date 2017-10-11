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
    'version' => '1.0.1',
    'constraints' => array(
        'depends' => array(
            'typo3' => '6.2.0-8.9.99',
		),
        'conflicts' => array(),
        'suggests' => array(),
	),
);
