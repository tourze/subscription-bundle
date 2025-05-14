<?php

namespace SubscriptionBundle\Tests\Enum;

use PHPUnit\Framework\TestCase;
use SubscriptionBundle\Enum\SubscribeStatus;

class SubscribeStatusTest extends TestCase
{
    public function testCases_withAllEnums(): void
    {
        // 测试所有枚举值
        $this->assertSame('active', SubscribeStatus::ACTIVE->value);
        $this->assertSame('expiry', SubscribeStatus::EXPIRED->value);
        
        // 测试枚举数量
        $cases = SubscribeStatus::cases();
        $this->assertCount(2, $cases);
        $this->assertContains(SubscribeStatus::ACTIVE, $cases);
        $this->assertContains(SubscribeStatus::EXPIRED, $cases);
    }
    
    public function testGetLabel_withActiveStatus(): void
    {
        // 测试活跃状态的标签
        $this->assertSame('活跃', SubscribeStatus::ACTIVE->getLabel());
    }
    
    public function testGetLabel_withExpiredStatus(): void
    {
        // 测试过期状态的标签
        $this->assertSame('过期', SubscribeStatus::EXPIRED->getLabel());
    }
    
    public function testFromString_withValidValues(): void
    {
        // 测试从字符串创建枚举
        $this->assertSame(SubscribeStatus::ACTIVE, SubscribeStatus::from('active'));
        $this->assertSame(SubscribeStatus::EXPIRED, SubscribeStatus::from('expiry'));
    }
    
    public function testFromString_withInvalidValues(): void
    {
        // 测试从无效字符串创建枚举时的异常
        $this->expectException(\ValueError::class);
        SubscribeStatus::from('invalid');
    }
    
    public function testTryFromString_withValidValues(): void
    {
        // 测试尝试从字符串创建枚举
        $this->assertSame(SubscribeStatus::ACTIVE, SubscribeStatus::tryFrom('active'));
        $this->assertSame(SubscribeStatus::EXPIRED, SubscribeStatus::tryFrom('expiry'));
    }
    
    public function testTryFromString_withInvalidValues(): void
    {
        // 测试尝试从无效字符串创建枚举
        $this->assertNull(SubscribeStatus::tryFrom('invalid'));
    }
} 