<?php

declare(strict_types=1);

namespace T3Monitor\T3monitoringClient\Provider;

/*
 * This file is part of the t3monitoring_client extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Core\Environment;

class ComposerInformationProvider implements DataProviderInterface
{
    public function get(array $data): array
    {
        $data['extra']['info']['Composer Usage'] = Environment::isComposerMode() ? 'yes' : 'no';
        return $data;
    }
}
