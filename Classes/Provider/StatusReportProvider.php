<?php
namespace T3Monitor\T3monitoringClient\Provider;

/*
 * This file is part of the t3monitoring_client extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
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
        $this->initialize();
        /** @var $statusReport \TYPO3\CMS\Reports\Report\Status\Status */
        $statusReport = GeneralUtility::makeInstance('TYPO3\\CMS\\Reports\\Report\\Status\\Status');
        $statusCollection = $statusReport->getSystemStatus();

        $severityConversion = array(
            Status::INFO => 'info',
            Status::WARNING => 'warning',
            Status::ERROR => 'danger',
        );
        foreach ($statusCollection as $statusProvider => $providerStatuses) {
            /** @var $status \TYPO3\CMS\Reports\Status */
            foreach ($providerStatuses as $status) {
                if ($status->getSeverity() > Status::OK) {
                    $title = sprintf('%s - %s', $status->getTitle(), $status->getValue());
                    $convertedSeverity = $severityConversion[$status->getSeverity()];
                    $data['extra'][$convertedSeverity][$title] = $status->getMessage();
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
            $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_reports']['status'] = array();
        }
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_reports']['status']['providers']['typo3'][] = 'TYPO3\\CMS\\Reports\\Report\\Status\\Typo3Status';
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_reports']['status']['providers']['system'][] = 'TYPO3\\CMS\\Reports\\Report\\Status\\SystemStatus';
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_reports']['status']['providers']['security'][] = 'TYPO3\\CMS\\Reports\\Report\\Status\\SecurityStatus';
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_reports']['status']['providers']['configuration'][] = 'TYPO3\\CMS\\Reports\\Report\\Status\\ConfigurationStatus';
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_reports']['status']['providers']['fal'][] = 'TYPO3\\CMS\\Reports\\Report\\Status\\FalStatus';
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_reports']['status']['providers']['typo3'][] = 'TYPO3\\CMS\\Install\\Report\\InstallStatusReport';
        if (GeneralUtility::compat_version('7.0')) {
            $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_reports']['status']['providers']['security'][] = 'TYPO3\\CMS\\Install\\Report\\SecurityStatusReport';
        }
    }

    /**
     * Returns LanguageService
     *
     * @return \TYPO3\CMS\Lang\LanguageService
     */
    protected function getLanguageService()
    {
        if ($GLOBALS['LANG'] === null) {
            $GLOBALS['LANG'] = GeneralUtility::makeInstance('TYPO3\\CMS\\Lang\\LanguageService');
        }
        return $GLOBALS['LANG'];
    }
}
