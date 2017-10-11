<?php

namespace T3Monitor\T3monitoringClient\Provider;

/*
 * This file is part of the t3monitoring_client extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Database\DatabaseConnection;

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
        $data['core']['typo3Version'] = TYPO3_version;
        $data['core']['phpVersion'] = substr(phpversion(), 0, strpos(phpversion() . '-', '-'));
        $data['core']['mysqlClientVersion'] = mysqli_get_client_version($this->getDatabaseConnection()->getDatabaseHandle());
        $data['core']['diskTotalSpace'] = disk_total_space(PATH_site);
        $data['core']['diskFreeSpace'] = disk_free_space(PATH_site);

        return $data;
    }

    /**
     * @return DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }
}
