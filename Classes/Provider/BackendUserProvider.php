<?php

namespace T3Monitor\T3monitoringClient\Provider;

/*
 * This file is part of the t3monitoring_client extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

class BackendUserProvider implements DataProviderInterface
{
    /**
     * @var \TYPO3\CMS\Beuser\Domain\Repository\BackendUserRepository
     */
    protected $backendUserRepository;

    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     */
    protected $objectManager;

    public function get(array $data)
    {
        $backendUsers = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', 'be_users', 'deleted=0');
        foreach ($backendUsers as $backendUser) {
            $backendUserData = array(
                'userName' => $backendUser['username'],
                'realName' => $backendUser['realName'],
                'emailAddress' => $backendUser['email'],
                'description' => $backendUser['description'],
                'lastLogin' => $backendUser['lastlogin'],
            );
            $data['users']['backend'][] = $backendUserData;
        }
        return $data;
    }
}
