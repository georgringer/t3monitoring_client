<?php
namespace T3Monitor\T3monitoringClient\Provider;

/*
 * This file is part of the t3monitoring_client extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class BackendUserProvider implements DataProviderInterface
{

    public function get(array $data)
    {
        $backendUsers =  GeneralUtility::_GP('backendUser');
        $data['backendUser'] = [];
        if (isset($backendUsers) && is_array($backendUsers) && count($backendUsers) > 0) {
            $table = 'be_users';
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);

            $users = [];
            foreach ($backendUsers as $backendUser) {
                $users[] = $queryBuilder->createNamedParameter($backendUser);
            }

            $rows = $queryBuilder
                ->select('username')
                ->from($table)
                ->where(
                    $queryBuilder->expr()->in('username', $users)
                )
                ->execute()
                ->fetchAll();

            foreach ($rows as $backendUser) {
                $data['backendUser'][] = $backendUser['username'];
            }
        };
        return $data;
    }
}