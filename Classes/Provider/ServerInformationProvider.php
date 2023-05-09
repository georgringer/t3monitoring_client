<?php

namespace T3Monitor\T3monitoringClient\Provider;

/*
 * This file is part of the t3monitoring_client extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ServerInformationProvider
 */
class ServerInformationProvider implements DataProviderInterface
{
    /**
     * @param array $data
     * @return array
     */
    public function get(array $data)
    {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionByName(ConnectionPool::DEFAULT_CONNECTION_NAME);
        $data['core']['typo3Version'] = GeneralUtility::makeInstance(Typo3Version::class)->getVersion();
        $data['core']['phpVersion'] = substr(phpversion(), 0, strpos(phpversion() . '-', '-'));
        $data['core']['mysqlClientVersion'] = $connection->getServerVersion();
        $data['core']['diskTotalSpace'] = disk_total_space(Environment::getProjectPath());
        $data['core']['diskFreeSpace'] = disk_free_space(Environment::getProjectPath());
        $data['core']['applicationContext'] = (string)Environment::getContext();
        return $data;
    }
}
