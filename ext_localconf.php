<?php
defined('TYPO3_MODE') || die('Access denied.');

$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['t3monitoring'] = \T3Monitor\T3monitoringClient\Client::class . '::run';

$GLOBALS['TYPO3_CONF_VARS']['EXT']['t3monitoring_client']['provider'][] = \T3Monitor\T3monitoringClient\Provider\StatusReportProvider::class;
$GLOBALS['TYPO3_CONF_VARS']['EXT']['t3monitoring_client']['provider'][] = \T3Monitor\T3monitoringClient\Provider\ServerInformationProvider::class;
$GLOBALS['TYPO3_CONF_VARS']['EXT']['t3monitoring_client']['provider'][] = \T3Monitor\T3monitoringClient\Provider\ComposerInformationProvider::class;
$GLOBALS['TYPO3_CONF_VARS']['EXT']['t3monitoring_client']['provider'][] = \T3Monitor\T3monitoringClient\Provider\ExtensionProvider::class;

// register to old slot if PSR-14 event is not present
if (!class_exists(TYPO3\CMS\Core\Package\Event\AfterPackageActivationEvent::class)) {
    $signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class);
    $signalSlotDispatcher->connect(
        \TYPO3\CMS\Extensionmanager\Utility\InstallUtility::class,
        'afterExtensionInstall',
        \T3Monitor\T3monitoringClient\Slots\ExtensionManagerSlot::class,
        'afterExtensionInstall'
    );
}
