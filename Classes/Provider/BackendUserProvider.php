<?php

namespace T3Monitor\T3monitoringClient\Provider;

/*
 * This file is part of the t3monitoring_client extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Beuser\Domain\Model\BackendUser;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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

        $this->objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
        $this->backendUserRepository = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Domain\\Repository\\BackendUserRepository');

        /** @var BackendUser[] $backendUsers */
        $backendUsers = $this->backendUserRepository->findAll();
        foreach($backendUsers as $backendUser)
        {
            $data['users']['backend'][] = array(
                'userName' => $backendUser->getUserName(),
                'realName' => $backendUser->getRealName(),
                'description' => $backendUser->getDescription(),
                'lastLogin' => $backendUser->getLastLoginDateAndTime()
            );
        }

        return $data;
    }
}