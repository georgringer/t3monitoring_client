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
use TYPO3\CMS\Scheduler\Task\AbstractTask;

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
            ->select('uid', 'description', 'nextexecution', 'lastexecution_time', 'lastexecution_failure', 'lastexecution_context', 'serialized_task_object')
            ->from('tx_scheduler_task')
            ->where(
                $qb->expr()->eq('disable', $qb->createNamedParameter(0, \PDO::PARAM_INT)),
                $qb->expr()->eq('deleted', $qb->createNamedParameter(0, \PDO::PARAM_INT))
            )
            ->execute();
        while ($row = $statement->fetch()) {
            $data['tasks'][] = $this->unserializeTask($row);
        }
        return $data;
    }

    /**
     * Unserialize a serialized task object. Fill some of the objects attributes
     * into the $task array and return it.
     *
     * @param array<string> $row The task row
     *
     * @return array<string> The task array
     */
    private function unserializeTask($row)
    {
        $task= unserialize($row['serialized_task_object']);
        $class = get_class($task);
        if ($class === \__PHP_Incomplete_Class::class && preg_match('/^O:[0-9]+:"(?P<classname>.+?)"/', $row['serialized_task_object'], $matches) === 1) {
            $class = $matches['classname'];
        }
        unset($row['serialized_task_object']);
        $row['class'] = $class;
        $row['interval'] = $task->getExecution()->getInterval();
        $row['cronCmd'] = $task->getExecution()->getCronCmd();
        $row['multiple'] = intval($task->getExecution()->getMultiple());

        if ($row['lastexecution_failure'] == null) {
            $row['lastexecution_failure'] = '';
        }

        if (!empty($row['interval']) || !empty($row['cronCmd'])) {
            $row['type'] = AbstractTask::TYPE_RECURRING;
            $row['frequency'] = $row['interval'] ?: $row['cronCmd'];
        } else {
            $row['type'] = AbstractTask::TYPE_SINGLE;
            $row['frequency'] = '';
        }
        unset($row['cronCmd'], $row['interval']);

        if ($row['nextexecution'] < $GLOBALS['EXEC_TIME']) {
            $row['late'] = true;
        } else {
            $row['late'] = false;
        }

        return $row;
    }
}
