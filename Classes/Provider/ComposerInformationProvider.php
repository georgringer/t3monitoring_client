<?php
namespace T3Monitor\T3monitoringClient\Provider;

/*
 * This file is part of the t3monitoring_client extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Core\Bootstrap;

/**
 * Class ComposerInformationProvider
 */
class ComposerInformationProvider implements DataProviderInterface
{

    /**
     * @param array $data
     * @return array
     */
    public function get(array $data)
    {
        $data['extra']['info']['Composer Usage'] = Bootstrap::usesComposerClassLoading() ? 'yes' : 'no';
        return $data;
    }
}
