<?php

namespace T3Monitor\T3monitoringClient\Provider;

/*
 * This file is part of the t3monitoring_client extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

/**
 * Class DummyDataProvider
 */
class DummyDataProvider implements DataProviderInterface
{
    /**
     * @param array $data
     * @return array
     */
    public function get(array $data)
    {
        $data['extra']['warning']['Warning 1'] = 'This is a warning';
        $data['extra']['warning']['Warning 2'] = 'This is another warning';
        $data['extra']['danger']['Danger 2'] = 'This is a critical message';

        return $data;
    }
}
