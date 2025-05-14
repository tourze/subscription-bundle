<?php

namespace SubscriptionBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use SubscriptionBundle\DependencyInjection\SubscriptionExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SubscriptionExtensionTest extends TestCase
{
    private SubscriptionExtension $extension;
    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $this->extension = new SubscriptionExtension();
        $this->container = new ContainerBuilder();
    }

    public function testLoad_withEmptyConfigs(): void
    {
        // 测试加载空配置
        $this->extension->load([], $this->container);
        
        // 验证服务是否已注册
        $this->assertTrue($this->container->has('SubscriptionBundle\Repository\PlanRepository'));
        $this->assertTrue($this->container->has('SubscriptionBundle\Repository\EquityRepository'));
        $this->assertTrue($this->container->has('SubscriptionBundle\Repository\RecordRepository'));
        $this->assertTrue($this->container->has('SubscriptionBundle\Repository\ResourceRepository'));
        $this->assertTrue($this->container->has('SubscriptionBundle\Repository\UsageRepository'));
    }
}