<?php

namespace Tourze\SubscriptionBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use Tourze\SubscriptionBundle\Entity\Plan;
use Tourze\SubscriptionBundle\Entity\Record;
use Tourze\SubscriptionBundle\Enum\SubscribeStatus;
use Symfony\Component\Security\Core\User\UserInterface;

class RecordTest extends TestCase
{
    private Record $record;

    protected function setUp(): void
    {
        $this->record = new Record();
    }

    public function testGettersAndSetters_withValidData(): void
    {
        // 创建模拟对象
        $user = $this->createMock(UserInterface::class);
        $plan = new Plan();
        $plan->setName('测试计划');
        
        // 设置基本属性
        $activeTime = new \DateTimeImmutable('2023-01-01');
        $expireTime = new \DateTimeImmutable('2023-12-31');
        $now = new \DateTimeImmutable();
        
        $this->record->setPlan($plan);
        $this->record->setUser($user);
        $this->record->setActiveTime($activeTime);
        $this->record->setExpireTime($expireTime);
        $this->record->setStatus(SubscribeStatus::ACTIVE);
        $this->record->setValid(true);
        $this->record->setCreatedBy('admin');
        $this->record->setUpdatedBy('admin');
        $this->record->setCreateTime($now);
        $this->record->setUpdateTime($now);
        
        // 验证结果
        $this->assertSame($plan, $this->record->getPlan());
        $this->assertSame($user, $this->record->getUser());
        $this->assertSame($activeTime, $this->record->getActiveTime());
        $this->assertSame($expireTime, $this->record->getExpireTime());
        $this->assertSame(SubscribeStatus::ACTIVE, $this->record->getStatus());
        $this->assertTrue($this->record->isValid());
        $this->assertSame('admin', $this->record->getCreatedBy());
        $this->assertSame('admin', $this->record->getUpdatedBy());
        $this->assertSame($now, $this->record->getCreateTime());
        $this->assertSame($now, $this->record->getUpdateTime());
    }

    public function testCreation_withDefaultValues(): void
    {
        // 测试默认值
        $this->assertSame(0, $this->record->getId());
        $this->assertFalse($this->record->isValid());
        $this->assertNull($this->record->getPlan());
        $this->assertNull($this->record->getUser());
        $this->assertNull($this->record->getActiveTime());
        $this->assertNull($this->record->getExpireTime());
        $this->assertNull($this->record->getStatus());
        $this->assertNull($this->record->getCreatedBy());
        $this->assertNull($this->record->getUpdatedBy());
        $this->assertNull($this->record->getCreateTime());
        $this->assertNull($this->record->getUpdateTime());
    }

    public function testSetStatus_withValidEnum(): void
    {
        // 设置激活状态
        $this->record->setStatus(SubscribeStatus::ACTIVE);
        $this->assertSame(SubscribeStatus::ACTIVE, $this->record->getStatus());
        
        // 设置过期状态
        $this->record->setStatus(SubscribeStatus::EXPIRED);
        $this->assertSame(SubscribeStatus::EXPIRED, $this->record->getStatus());
        
        // 设置空状态
        $this->record->setStatus(null);
        $this->assertNull($this->record->getStatus());
    }

    public function testIsValid_withDifferentValues(): void
    {
        // 测试设置有效
        $this->record->setValid(true);
        $this->assertTrue($this->record->isValid());
        
        // 测试设置无效
        $this->record->setValid(false);
        $this->assertFalse($this->record->isValid());
        
        // 测试设置为null
        $this->record->setValid(null);
        $this->assertNull($this->record->isValid());
    }

    public function testSetPlan_withPlanObject(): void
    {
        // 创建计划对象
        $plan = new Plan();
        $plan->setName('测试计划');
        
        // 设置计划
        $result = $this->record->setPlan($plan);
        
        // 验证结果
        $this->assertSame($this->record, $result); // 返回自身以支持链式调用
        $this->assertSame($plan, $this->record->getPlan());
    }

    public function testSetPlan_withNull(): void
    {
        // 设置为null
        $result = $this->record->setPlan(null);
        
        // 验证结果
        $this->assertSame($this->record, $result); // 返回自身以支持链式调用
        $this->assertNull($this->record->getPlan());
    }
} 