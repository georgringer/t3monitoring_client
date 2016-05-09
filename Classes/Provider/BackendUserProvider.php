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
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Service\ImageService;

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
        $this->backendUserRepository = $this->objectManager->get('TYPO3\\CMS\\Beuser\\Domain\\Repository\\BackendUserRepository');

        /** @var BackendUser[] $backendUsers */
        $backendUsers = $this->backendUserRepository->findAll();
        foreach($backendUsers as $backendUser)
        {
            $backendUserData = array(
                'userName' => $backendUser->getUserName(),
                'realName' => $backendUser->getRealName(),
                'emailAddress' => $backendUser->getEmail(),
                'description' => $backendUser->getDescription(),
                'lastLogin' => $backendUser->getLastLoginDateAndTime(),
                'avatar' => ''
            );
            if (GeneralUtility::compat_version('7.5')) {
                $backendUserData['avatar'] = $this->getAvatarUrl($backendUser->getUid());
            }
            $data['users']['backend'][] = $backendUserData;
        }
        return $data;
    }

    protected function getAvatarUrl($beUserUid)
    {
        $fileRepository = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Resource\\FileRepository');
        $fileObjects = $fileRepository->findByRelation('be_users', 'avatar', $beUserUid);
        if(count($fileObjects) > 0)
        {
            $fileObject = $fileObjects[0];
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            $imageService = $objectManager->get(ImageService::class);
            $processingInstructions = array(
                'width' => '32c',
                'height' => '32',
            );
            $processedImage = $imageService->applyProcessingInstructions($fileObject, $processingInstructions);
            return $imageService->getImageUri($processedImage);
        }
        return NULL;
    }
}