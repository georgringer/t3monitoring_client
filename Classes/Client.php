<?php

namespace T3Monitor\T3monitoringClient;

/*
 * This file is part of the t3monitoring_client extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use T3Monitor\T3monitoringClient\Provider\DataProviderInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Http\ServerRequestFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class Client
 */
class Client
{
    /**
     * Entry point
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function run(ServerRequestInterface $request): ResponseInterface
    {
        $settings = $this->getSettings();

        $response = GeneralUtility::makeInstance(Response::class);
        $error = $this->checkAccess($request);
        if ($error) {
            $response = $response->withStatus(403);
            if (!empty($settings['enableDebugForErrors'])) {
                $response->getBody()->write($error);
            }
            return $response;
        }

        if (method_exists(Bootstrap::class, 'initializeBackendRouter')) {
            Bootstrap::initializeBackendRouter();
        }
        Bootstrap::loadExtTables();

        $data = $this->collectData();
        $data = $this->utf8Converter($data);

        // Generate json
        if ($output = json_encode($data, JSON_THROW_ON_ERROR)) {
            $response = $response->withHeader('Content-Type', 'application/json; charset=utf-8');
            $response->getBody()->write($output);
        } else {
            $response = $response->withStatus(403);
            if (!empty($settings['enableDebugForErrors'])) {
                $response->getBody()->write('ERROR: Problems while encoding Json');
            }
        }
        return $response;
    }

    /**
     * Convert array to UTF-8
     *
     * @param string[] $array
     * @return array
     */
    protected function utf8Converter(array $array)
    {
        array_walk_recursive($array, function (&$item) {
            if (!mb_detect_encoding((string)$item, 'utf-8', true)) {
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
        $data = [];
        $classes = (array)$GLOBALS['TYPO3_CONF_VARS']['EXT']['t3monitoring_client']['provider'] ?? [];

        if (empty($classes)) {
            $data['error'] = 'No providers';
        } else {
            // Since 10.4.16, the internal ExtensionProvider requires a request
            if (!($GLOBALS['TYPO3_REQUEST'] ?? null)) {
                $request = ServerRequestFactory::fromGlobals();
                $GLOBALS['TYPO3_REQUEST'] = $request->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_FE);
            }

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
     * @param ServerRequestInterface $request
     * @return string
     */
    protected function checkAccess(ServerRequestInterface $request): string
    {
        $settings = $this->getSettings();

        // secret
        if (!empty($settings['secret']) && strlen($settings['secret']) >= 5) {
            $secret = $request->getQueryParams()['secret'] ?? '';
            if ($secret !== $settings['secret']) {
                return sprintf('Secret wrong, provided was "%s"', $secret);
            }
        } else {
            return 'No secret or too small secret defined';
        }

        /* Only returns an error when both are empty */
        if (empty($settings['allowedIps']) && empty($settings['allowedDomains'])) {
            return 'No allowed ips or domains defined';
        }

        $allowedIps = $this->getAllowedIps($settings['allowedIps'], $settings['allowedDomains']);

        $remoteIp = GeneralUtility::getIndpEnv('REMOTE_ADDR');
        if (!GeneralUtility::cmpIP($remoteIp, $allowedIps)) {
            return sprintf('IP comparison failed, remote IP: %s!', $remoteIp);
        }

        return '';
    }

    /**
     * Parses the allowed domains and IPs from the extension settings into a single allowed IPs string
     * @return string The allowed ips, comma separated.
     */
    public function getAllowedIps(string $allowedIps, string $allowedDomains): string
    {
        $allowedIps = trim($allowedIps);
        $allowedDomains = trim($allowedDomains);

        /* Return * when anything is allowed in one field, and the other is empty */
        if (($allowedIps === '*' && $allowedDomains === '*') ||
            ($allowedIps === '*' && empty($allowedDomains)) ||
            (empty($allowedIps) && $allowedDomains === '*')) {
            return '*';
        }

        $allowedIpsResult = '';
        if($allowedIps !== '*' && !empty($allowedIps)) {
            $allowedIpsResult = sprintf('%s, ', $allowedIps);
        }

        /* Convert the domain names to IP addresses */
        if($allowedDomains !== '*' && !empty($allowedDomains)) {
            $allowedDomainsArray = explode(',', $allowedDomains);
            foreach ($allowedDomainsArray as $allowedDomain) {
                $resultsIpv4 = @dns_get_record($allowedDomain, DNS_A);
                if($resultsIpv4) {
                    foreach ($resultsIpv4 as $resultIpv4) {
                        if(isset($resultIpv4['ip'])) {
                            $allowedIpsResult .= sprintf('%s, ', $resultIpv4['ip']);
                        }
                    }
                }
                $resultsIpv6 = @dns_get_record($allowedDomain, DNS_AAAA);
                if($resultsIpv6) {
                    foreach ($resultsIpv6 as $resultIpv6) {
                        if(isset($resultIpv6['ipv6'])) {
                            $allowedIpsResult .= sprintf('%s, ', $resultIpv6['ipv6']);
                        }
                    }
                }
            }
        }

        /* Return concatenated IPs and domains IPs */
        return trim($allowedIpsResult, ' ,');
    }

    protected function getSettings(): array
    {
        $configuration = [];
        try {
            $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);
            $configuration = $extensionConfiguration->get('t3monitoring_client');
        } catch (\Exception $exception) {
            // do nothing
        }

        return $configuration;
    }
}
