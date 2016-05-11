<?php
namespace T3Monitor\T3monitoringClient\Provider;

/*
 * This file is part of the t3monitoring_client extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extensionmanager\Utility\EmConfUtility;
use TYPO3\CMS\Extensionmanager\Utility\ListUtility;

/**
 * Class Extension6xProvider
 */
class Extension6xProvider implements DataProviderInterface
{

    /**
     * @param array $data
     * @return array
     * @throws \BadFunctionCallException
     */
    public function get(array $data)
    {
        $GLOBALS['LANG'] = GeneralUtility::makeInstance('TYPO3\\CMS\\Lang\\LanguageService');
        $GLOBALS['LANG']->init('default');

        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
        /** @var ListUtility $listUtility */
        $listUtility = $objectManager->get('TYPO3\\CMS\\Extensionmanager\\Utility\\ListUtility');

        $allExtensions = $listUtility->getAvailableExtensions();

        /** @var EmConfUtility $emConfUtility */
        $emConfUtility = GeneralUtility::makeInstance('TYPO3\\CMS\\Extensionmanager\\Utility\\EmConfUtility');
        foreach ($allExtensions as $key => $f) {
            if (is_dir(PATH_site . 'typo3/sysext/' . $key . '/')) {
                continue;
            }
            $data['extensions'][$key] = $emConfUtility->includeEmConf($f);
            $data['extensions'][$key]['isLoaded'] = (int)ExtensionManagementUtility::isLoaded($key);
        }

        return $data;
    }
}
