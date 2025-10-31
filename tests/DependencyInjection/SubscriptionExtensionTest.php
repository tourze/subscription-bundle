<?php

namespace Tourze\SubscriptionBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;
use Tourze\SubscriptionBundle\DependencyInjection\SubscriptionExtension;

/**
 * @internal
 */
#[CoversClass(SubscriptionExtension::class)]
final class SubscriptionExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
}
