<?php

namespace Tourze\SubscriptionBundle\DependencyInjection;

use Tourze\SymfonyDependencyServiceLoader\AutoExtension;

class SubscriptionExtension extends AutoExtension
{
    protected function getConfigDir(): string
    {
        return __DIR__ . '/../Resources/config';
    }
}
