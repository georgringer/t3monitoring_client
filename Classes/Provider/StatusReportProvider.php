<?php

namespace T3Monitor\T3monitoringClient\Provider;

/*
 * This file is part of the t3monitoring_client extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Report\InstallStatusReport;
use TYPO3\CMS\Install\Report\SecurityStatusReport;
use TYPO3\CMS\Reports\Report\Status as Stati;
use TYPO3\CMS\Reports\Status;

/**
 * Class StatusReportProvider
 */
class StatusReportProvider implements DataProviderInterface
{

    /**
     * @param array $data
     * @return array
     */
    public function get(array $data)
    {
        if (ExtensionManagementUtility::isLoaded('reports')) {
            $this->initialize();
            $statusReport = GeneralUtility::makeInstance(Stati\Status::class);
            $statusCollection = $statusReport->getSystemStatus();

            $severityConversion = [
                Status::INFO => 'info',
                Status::WARNING => 'warning',
                Status::ERROR => 'danger',
            ];
            foreach ($statusCollection as $statusProvider => $providerStatuses) {
                /** @var $status Status */
                foreach ($providerStatuses as $status) {
                    if ($status->getSeverity() > Status::OK) {
                        $title = sprintf('%s - %s', $status->getTitle(), $status->getValue());
                        $convertedSeverity = $severityConversion[$status->getSeverity()];
                        $data['extra'][$convertedSeverity][$title] = $status->getMessage();
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Initialize some code which is usually only available in backend context
     *
     * @return void
     */
    protected function initialize()
    {
        $this->getLanguageService()->init('en');
        if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_reports']['status'])) {
            $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_reports']['status'] = [];
        }

        $skippedReports = [
            InstallStatusReport::class
        ];

        foreach($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_reports']['status']['providers'] as $provider => $providerStati) {
            $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_reports']['status']['providers'][$provider] = array_diff($providerStati, $skippedReports);
        }

        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_reports']['status']['providers']['typo3'][] = Stati\Typo3Status::class;
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_reports']['status']['providers']['system'][] = Stati\SystemStatus::class;
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_reports']['status']['providers']['security'][] = Stati\SecurityStatus::class;
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_reports']['status']['providers']['configuration'][] = Stati\ConfigurationStatus::class;
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_reports']['status']['providers']['fal'][] = Stati\FalStatus::class;
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_reports']['status']['providers']['security'][] = SecurityStatusReport::class;
    }

    /**
     * @return LanguageService
     */
    protected function getLanguageService(): LanguageService
    {
        if (!isset($GLOBALS['LANG'])) {
            $GLOBALS['LANG'] = GeneralUtility::makeInstance(LanguageService::class);
        }
        return $GLOBALS['LANG'];
    }
}
