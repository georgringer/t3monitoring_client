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

/**
 * Class TaskProvider
 *
 */
class TaskProvider implements DataProviderInterface
{
    /**
     * Reads the scheduled tasks from the database and adds them to the data.
     *
     * @param array $data Client data
     *
     * @return array Client data with tasks
     */
    public function get(array $data): array
    {
        $qb = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_scheduler_task');
        $statement = $qb
            ->select('uid', 'description', 'nextexecution', 'lastexecution_time', 'lastexecution_failure', 'lastexecution_context')
            ->from('tx_scheduler_task')
            ->where(
                $qb->expr()->eq('disable', $qb->createNamedParameter(0, \PDO::PARAM_INT))
            )
            ->execute();
        while ($row = $statement->fetch()) {
            $data['tasks'][] = $row;
        }
        return $data;
    }
}
