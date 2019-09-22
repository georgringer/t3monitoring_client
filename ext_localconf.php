<?php
defined('TYPO3_MODE') || die('Access denied.');

$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['t3monitoring'] = 'EXT:t3monitoring_client/Classes/Client.php';

$GLOBALS['TYPO3_CONF_VARS']['EXT']['t3monitoring_client']['provider'][] = 'T3Monitor\\T3monitoringClient\\Provider\\StatusReportProvider';

if (version_compare(TYPO3_branch, '9.0', '>=')) {
    $version = 9;
} elseif (version_compare(TYPO3_branch, '8.0', '>=')) {
    $version = 8;
} elseif (version_compare(TYPO3_branch, '7.0', '>=')) {
    $version = 7;
} else {
    $version = 6;
}

if ($version >= 8) {
    $GLOBALS['TYPO3_CONF_VARS']['EXT']['t3monitoring_client']['provider'][] = 'T3Monitor\\T3monitoringClient\\Provider\\ServerInformation8xProvider';
    $GLOBALS['TYPO3_CONF_VARS']['EXT']['t3monitoring_client']['provider'][] = 'T3Monitor\\T3monitoringClient\\Provider\\ConfigurationProvider';
    $GLOBALS['TYPO3_CONF_VARS']['EXT']['t3monitoring_client']['provider'][] = 'T3Monitor\\T3monitoringClient\\Provider\\BackendUserProvider';
} else {
    $GLOBALS['TYPO3_CONF_VARS']['EXT']['t3monitoring_client']['provider'][] = 'T3Monitor\\T3monitoringClient\\Provider\\ServerInformationProvider';
}

if ($version >= 7) {
    $GLOBALS['TYPO3_CONF_VARS']['EXT']['t3monitoring_client']['provider'][] = 'T3Monitor\\T3monitoringClient\\Provider\\ComposerInformationProvider';
    $GLOBALS['TYPO3_CONF_VARS']['EXT']['t3monitoring_client']['provider'][] = 'T3Monitor\\T3monitoringClient\\Provider\\Extension7xProvider';
} else {
    $GLOBALS['TYPO3_CONF_VARS']['EXT']['t3monitoring_client']['provider'][] = 'T3Monitor\\T3monitoringClient\\Provider\\Extension6xProvider';
}

//$GLOBALS['TYPO3_CONF_VARS']['EXT']['t3monitoring_client']['provider'][] = 'T3Monitor\\T3monitoringClient\\Provider\\DummyDataProvider';

/** @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher */
$signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\SignalSlot\\Dispatcher');
$signalSlotDispatcher->connect(
    'TYPO3\\CMS\\Extensionmanager\\Utility\\InstallUtility',
    'afterExtensionInstall',
    'T3Monitor\\T3monitoringClient\\Slots\\ExtensionManagerSlot',
    'afterExtensionInstall'
);
