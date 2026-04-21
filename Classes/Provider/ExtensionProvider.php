<?php

declare(strict_types=1);

namespace T3Monitor\T3monitoringClient\Provider;

/*
 * This file is part of the t3monitoring_client extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Package\Exception\UnknownPackageException;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extensionmanager\Utility\EmConfUtility;
use TYPO3\CMS\Extensionmanager\Utility\ListUtility;

class ExtensionProvider implements DataProviderInterface
{
    public function get(array $data): array
    {
        $listUtility = GeneralUtility::makeInstance(ListUtility::class);
        $packageManager = GeneralUtility::makeInstance(PackageManager::class);
        $emConfUtility = GeneralUtility::makeInstance(EmConfUtility::class);

        $allExtensions = $listUtility->getAvailableExtensions();

        foreach ($allExtensions as $key => $f) {
            $extensionConfig = (array)$emConfUtility->includeEmConf($key, $f['packagePath']);
            if (!array_filter($extensionConfig)) {
                $extensionConfig = $f;
            }
            if (($extensionConfig['type'] ?? '') === 'System' || ($extensionConfig['author'] ?? '') === 'TYPO3 Core Team') {
                continue;
            }

            $data['extensions'][$key] = $extensionConfig;
            $data['extensions'][$key]['isLoaded'] = (int)ExtensionManagementUtility::isLoaded($key);
            $data['extensions'][$key]['composerName'] = $this->getComposerName($packageManager, $key);
        }

        return $data;
    }

    /**
     * Get the composer package name for an extension
     *
     * @param PackageManager $packageManager
     * @param string $extensionKey
     * @return string|null Returns the composer name (e.g., "vendor/package") or null if not available
     */
    private function getComposerName(PackageManager $packageManager, string $extensionKey): ?string
    {
        try {
            $package = $packageManager->getPackage($extensionKey);
            $composerName = $package->getValueFromComposerManifest('name');
            return is_string($composerName) ? $composerName : null;
        } catch (UnknownPackageException) {
            return null;
        }
    }
}
