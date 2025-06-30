<?php

namespace Tourze\SubscriptionBundle\Tests;

use PHPUnit\Framework\TestCase;
use Tourze\SubscriptionBundle\SubscriptionBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SubscriptionBundleTest extends TestCase
{
    public function testBundleInstance_withCreation(): void
    {
        // 创建Bundle实例
        $bundle = new SubscriptionBundle();

        // 验证实例是否为Bundle实例
        $this->assertInstanceOf(Bundle::class, $bundle);
        $this->assertInstanceOf(SubscriptionBundle::class, $bundle);
    }
}
