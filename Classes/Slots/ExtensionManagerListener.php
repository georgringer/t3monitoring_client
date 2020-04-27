<?php
declare(strict_types=1);

namespace T3Monitor\T3monitoringClient\Slots;

use TYPO3\CMS\Core\Package\Event\AfterPackageActivationEvent;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ExtensionManagerListener
{
    public function __invoke(AfterPackageActivationEvent $event): void
    {
        // use existing slot functionality
        GeneralUtility::makeInstance(ExtensionManagerSlot::class)
            ->afterExtensionInstall($event->getPackageKey());
    }
}