<?php

namespace T3Monitor\T3monitoringClient;

/*
 * This file is part of the t3monitoring_client extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Exception;
use T3Monitor\T3monitoringClient\Client as ClientService;
use T3Monitor\T3monitoringClient\Provider\DataProviderInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;

/**
 * Class Client
 */
class Client
{

    /**
     * Entry point
     * @throws \Exception
     */
    public function run()
    {
        $settings = (array)unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['t3monitoring_client']);

        if (!$this->checkAccess()) {
            HttpUtility::setResponseCodeAndExit(HttpUtility::HTTP_STATUS_403);
        }

        $data = $this->collectData();
        $data = $this->utf8Converter($data);

        // Generate json
        if ($output = json_encode($data)) {
            echo $output;
        } else {
            if (isset($settings['enableDebugForErrors']) && (int)$settings['enableDebugForErrors'] === 1) {
                echo 'ERROR: Problems while encoding Json';
            }
            HttpUtility::setResponseCodeAndExit(HttpUtility::HTTP_STATUS_403);
        }
    }

    /**
     * Convert array to UTF-8
     *
     * @param $array
     * @return array
     */
    protected function utf8Converter($array)
    {
        array_walk_recursive($array, function (&$item, $key) {
            if (!mb_detect_encoding($item, 'utf-8', true)) {
                $item = utf8_encode($item);
            }
        });

        return $array;
    }

    /**
     * Collect data
     *
     * @return array
     */
    protected function collectData()
    {
        $data = array();
        $classes = (array)$GLOBALS['TYPO3_CONF_VARS']['EXT']['t3monitoring_client']['provider'];

        if (empty($classes)) {
            $data['error'] = 'No providers';
        } else {
            foreach ($classes as $class) {
                /** @var DataProviderInterface $call */
                $call = GeneralUtility::makeInstance($class);
                if (!$call instanceof DataProviderInterface) {
                    $data['error'] = sprintf('The class "%s" does not implements "%s"!', $call, $class);
                    return $data;
                }
                $data = $call->get($data);
            }
        }
        return $data;
    }

    /**
     * Check if access is allowed to the endpoint
     *
     * @return bool
     * @throws \Exception
     */
    protected function checkAccess()
    {
        $settings = (array)unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['t3monitoring_client']);

        try {
            // secret
            if (isset($settings['secret']) && !empty($settings['secret']) && strlen($settings['secret']) >= 5) {
                $secret = GeneralUtility::_GET('secret');
                if ($secret !== $settings['secret']) {
                    throw new Exception(sprintf('Secret wrong, provided was "%s"', $secret));
                }
            } else {
                throw new Exception('No secret or too small secret defined');
            }

            if (!isset($settings['allowedIps']) || empty($settings['allowedIps'])) {
                throw new Exception('No allowed ips defined');
            }
            $remoteIp = GeneralUtility::getIndpEnv('REMOTE_ADDR');
            if (!GeneralUtility::cmpIP($remoteIp, $settings['allowedIps'])) {
                throw new Exception(sprintf('IP comparison failed, remote IP: %s!', $remoteIp));
            }
        } catch (Exception $e) {
            if (isset($settings['enableDebugForErrors']) && (int)$settings['enableDebugForErrors'] === 1) {
                echo('ERROR: ' . htmlspecialchars($e->getMessage()));
                HttpUtility::setResponseCodeAndExit(HttpUtility::HTTP_STATUS_403);
            }
            return false;
        }

        return true;
    }
}

/** @var ClientService $client */
$client = GeneralUtility::makeInstance('T3Monitor\\T3monitoringClient\\Client');
$client->run();
