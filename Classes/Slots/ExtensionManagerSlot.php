<?php

namespace T3Monitor\T3monitoringClient\Slots;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Configuration\ConfigurationManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ExtensionManagerSlot
{
    const SECRET_LENGTH = 50;

    /**
     * @param string $extensionKey
     */
    public function afterExtensionInstall($extensionKey)
    {
        if ($extensionKey === 't3monitoring_client') {

            $configuration = $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['t3monitoring_client'];
            if (!isset($configuration)) {
                $configuration = array();
            } else {
                $configuration = unserialize($configuration);
            }

            if (!isset($configuration['secret']) || !empty($configuration['secret'])) {
                $configuration['secret'] = GeneralUtility::getRandomHexString(self::SECRET_LENGTH);
                /** @var $configurationManager ConfigurationManager */
                $configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
                $configurationManager->setLocalConfigurationValueByPath(
                    'EXT/extConf/' . $extensionKey,
                    serialize($configuration)
                );
            }
        }
    }
}