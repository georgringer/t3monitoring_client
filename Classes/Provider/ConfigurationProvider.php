<?php

namespace T3Monitor\T3monitoringClient\Provider;

use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ConfigurationProvider implements DataProviderInterface
{
    /**
     * Blind configuration options which should not be visible
     *
     * Source: \TYPO3\CMS\Lowlevel\View\ConfigurationView
     *
     * @var array
     */
    protected static $blindedConfigurationOptions = [
        'TYPO3_CONF_VARS' => [
            'DB' => [
                'database' => '******',
                'host' => '******',
                'password' => '******',
                'port' => '******',
                'socket' => '******',
                'username' => '******',
                'Connections' => [
                    'Default' => [
                        'dbname' => '******',
                        'host' => '******',
                        'password' => '******',
                        'port' => '******',
                        'user' => '******',
                        'unix_socket' => '******',
                    ],
                ],
            ],
            'SYS' => [
                'encryptionKey' => '******'
            ]
        ]
    ];

    public function get(array $data)
    {
        $configuration = $GLOBALS['TYPO3_CONF_VARS'];
        ArrayUtility::mergeRecursiveWithOverrule(
            $configuration,
            ArrayUtility::intersectRecursive(self::$blindedConfigurationOptions['TYPO3_CONF_VARS'], $configuration)
        );

        $configurationValues = GeneralUtility::_GP('configurationValue');
        foreach ($configurationValues as $path) {
            if (ArrayUtility::isValidPath($configuration, $path)) {
                $data['configuration'][$path] = ArrayUtility::getValueByPath($configuration, $path);
            }
        }

        return $data;
    }
}