<?php

namespace T3Monitor\T3monitoringClient\Slots;

/*
 * This file is part of the t3monitoring_client extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Crypto\Random;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ExtensionManagerSlot
{
    const SECRET_LENGTH = 50;

    /** @var ExtensionConfiguration */
    protected $extensionConfiguration;

    /** @var Random */
    protected $random;

    public function __construct()
    {
        $this->extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);
        $this->random = GeneralUtility::makeInstance(Random::class);
    }

    /**
     * @param string $extensionKey
     */
    public function afterExtensionInstall($extensionKey)
    {
        if ($extensionKey === 't3monitoring_client') {
            try {
                $configuration = $this->extensionConfiguration->get($extensionKey);
            } catch (\Exception $exception) {
                $configuration = [];
            }

            if (empty($configuration['secret'])) {
                $secret = $this->random->generateRandomHexString(self::SECRET_LENGTH);
                $configuration['secret'] = $secret;

                $this->extensionConfiguration->set($extensionKey, '', $configuration);
            }
        }
    }
}
