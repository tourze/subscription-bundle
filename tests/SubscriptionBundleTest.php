<?php

declare(strict_types=1);

namespace Tourze\SubscriptionBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;
use Tourze\SubscriptionBundle\SubscriptionBundle;

/**
 * @internal
 */
#[CoversClass(SubscriptionBundle::class)]
#[RunTestsInSeparateProcesses]
final class SubscriptionBundleTest extends AbstractBundleTestCase
{
}
