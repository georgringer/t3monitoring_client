<?php

declare(strict_types=1);

namespace T3Monitor\T3monitoringClient\EventListener;

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Crypto\Random;
use TYPO3\CMS\Core\Package\Event\AfterPackageActivationEvent;

final class ExtensionManagerListener
{
    public const SECRET_LENGTH = 50;

    private ExtensionConfiguration $extensionConfiguration;
    private Random $random;

    public function __construct(ExtensionConfiguration $extensionConfiguration, Random $random)
    {
        $this->extensionConfiguration = $extensionConfiguration;
        $this->random = $random;
    }

    public function __invoke(AfterPackageActivationEvent $event): void
    {
        if ($event->getPackageKey() === 't3monitoring_client') {
            try {
                $configuration = $this->extensionConfiguration->get($event->getPackageKey());
            } catch (\Exception $exception) {
                $configuration = [];
            }

            if (empty($configuration['secret'])) {
                $secret = $this->random->generateRandomHexString(self::SECRET_LENGTH);
                $configuration['secret'] = $secret;

                $this->extensionConfiguration->set($event->getPackageKey(), $configuration);
            }
        }
    }
}
