<?php
namespace T3Monitor\T3monitoringClient\Slots;

/*
 * This file is part of the t3monitoring_client extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Configuration\ConfigurationManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ExtensionManagerSlot
 */
class ExtensionManagerSlot
{
    const SECRET_LENGTH = 50;

    /**
     * @param string $extensionKey
     */
    public function afterExtensionInstall($extensionKey)
    {
        if ($extensionKey === 't3monitoring_client') {
            if (!isset($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['t3monitoring_client'])) {
                $configuration = array();
            } else {
                $configuration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['t3monitoring_client']);
            }

            if (empty($configuration['secret'])) {
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
