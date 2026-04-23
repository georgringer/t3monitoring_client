<?php

declare(strict_types=1);

namespace T3Monitor\T3monitoringClient\Provider;

/*
 * This file is part of the t3monitoring_client extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class StatusReportProvider implements DataProviderInterface
{
    public function get(array $data): array
    {
        if (!ExtensionManagementUtility::isLoaded('reports')) {
            return $data;
        }
        $version = new Typo3Version();

        if (!($GLOBALS['LANG'] ?? null)) {
            $GLOBALS['LANG'] = GeneralUtility::makeInstance(LanguageServiceFactory::class)->create('default');
        }

        if ($version->getMajorVersion() < 14) {
            $statusReport = GeneralUtility::makeInstance(\TYPO3\CMS\Reports\Report\Status\Status::class);
            $stati = $statusReport->getSystemStatus();
        } else {
            $statusService = GeneralUtility::makeInstance(\TYPO3\CMS\Reports\Service\StatusService::class);
            $stati = $statusService->getSystemStatus($GLOBALS['TYPO3_REQUEST'] ?? null);
        }
        foreach ($stati as $providerStatuses) {
            foreach ($providerStatuses as $status) {
                if ($status->getSeverity()->value > ContextualFeedbackSeverity::OK->value) {
                    $title = sprintf('%s - %s', $status->getTitle(), $status->getValue());
                    $data['extra'][$status->getSeverity()->name][$title] = $status->getMessage();
                }
            }
        }

        return $data;
    }
}
