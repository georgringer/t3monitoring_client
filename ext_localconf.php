<?php

use T3Monitor\T3monitoringClient\Client;
use T3Monitor\T3monitoringClient\Provider\ComposerInformationProvider;
use T3Monitor\T3monitoringClient\Provider\ExtensionProvider;
use T3Monitor\T3monitoringClient\Provider\ServerInformationProvider;
use T3Monitor\T3monitoringClient\Provider\StatusReportProvider;

defined('TYPO3') || die('Access denied.');

$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['t3monitoring'] = Client::class . '::run';

$GLOBALS['TYPO3_CONF_VARS']['EXT']['t3monitoring_client']['provider'][] = StatusReportProvider::class;
$GLOBALS['TYPO3_CONF_VARS']['EXT']['t3monitoring_client']['provider'][] = ServerInformationProvider::class;
$GLOBALS['TYPO3_CONF_VARS']['EXT']['t3monitoring_client']['provider'][] = ComposerInformationProvider::class;
$GLOBALS['TYPO3_CONF_VARS']['EXT']['t3monitoring_client']['provider'][] = ExtensionProvider::class;
