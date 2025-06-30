<?php

namespace Tourze\SubscriptionBundle\Tests\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Tourze\SubscriptionBundle\Entity\Equity;
use Tourze\SubscriptionBundle\Entity\Plan;

class EquityTest extends TestCase
{
    private Equity $equity;

    protected function setUp(): void
    {
        $this->equity = new Equity();
    }

    public function testGettersAndSetters_withValidData(): void
    {
        // 测试基本属性
        $this->equity->setName('测试权益');
        $this->equity->setDescription('这是一个测试权益');
        $this->equity->setType('credit');
        $this->equity->setValue('100');
        $this->equity->setCreatedBy('admin');
        $this->equity->setUpdatedBy('admin');

        $now = new \DateTimeImmutable();
        $this->equity->setCreateTime($now);
        $this->equity->setUpdateTime($now);

        // 验证结果
        $this->assertSame('测试权益', $this->equity->getName());
        $this->assertSame('这是一个测试权益', $this->equity->getDescription());
        $this->assertSame('credit', $this->equity->getType());
        $this->assertSame('100', $this->equity->getValue());
        $this->assertSame('admin', $this->equity->getCreatedBy());
        $this->assertSame('admin', $this->equity->getUpdatedBy());
        $this->assertSame($now, $this->equity->getCreateTime());
        $this->assertSame($now, $this->equity->getUpdateTime());
    }

    public function testCreation_withDefaultValues(): void
    {
        // 测试默认值
        $this->assertSame(0, $this->equity->getId());
        $this->assertNull($this->equity->getDescription());
        $this->assertSame('0', $this->equity->getValue());
        $this->assertNull($this->equity->getCreatedBy());
        $this->assertNull($this->equity->getUpdatedBy());
        $this->assertNull($this->equity->getCreateTime());
        $this->assertNull($this->equity->getUpdateTime());
        $this->assertInstanceOf(ArrayCollection::class, $this->equity->getPlans());
        $this->assertCount(0, $this->equity->getPlans());
    }

    public function testAddPlan_withValidPlan(): void
    {
        // 准备测试数据
        $plan = new Plan();
        $plan->setName('测试计划');

        // 添加计划
        $result = $this->equity->addPlan($plan);

        // 验证结果
        $this->assertSame($this->equity, $result); // 返回自身以支持链式调用
        $this->assertCount(1, $this->equity->getPlans());
        $this->assertTrue($this->equity->getPlans()->contains($plan));
    }

    public function testAddPlan_withDuplicatePlan(): void
    {
        // 准备测试数据
        $plan = new Plan();
        $plan->setName('测试计划');

        // 添加两次相同的计划
        $this->equity->addPlan($plan);
        $this->equity->addPlan($plan);

        // 验证结果 - 应该只添加一次
        $this->assertCount(1, $this->equity->getPlans());
    }

    public function testRemovePlan_withExistingPlan(): void
    {
        // 准备测试数据
        $plan = new Plan();
        $plan->setName('测试计划');
        $this->equity->addPlan($plan);

        // 移除计划
        $result = $this->equity->removePlan($plan);

        // 验证结果
        $this->assertSame($this->equity, $result); // 返回自身以支持链式调用
        $this->assertCount(0, $this->equity->getPlans());
        $this->assertFalse($this->equity->getPlans()->contains($plan));
    }

    public function testRemovePlan_withNonExistingPlan(): void
    {
        // 准备测试数据
        $plan = new Plan();
        $plan->setName('测试计划');

        // 移除未添加的计划
        $result = $this->equity->removePlan($plan);

        // 验证结果 - 不应抛出异常，而是静默失败
        $this->assertSame($this->equity, $result);
        $this->assertCount(0, $this->equity->getPlans());
    }

    public function testToString_withValidId(): void
    {
        // 使用反射设置私有属性
        $reflectionClass = new \ReflectionClass(Equity::class);
        $idProperty = $reflectionClass->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($this->equity, 1);

        $this->equity->setName('测试权益');

        // 测试__toString方法
        $this->assertEquals('测试权益', (string)$this->equity);
    }

    public function testToString_withoutId(): void
    {
        $this->equity->setName('测试权益');

        // ID为0时应返回'new Equity'
        $this->assertEquals('new Equity', (string)$this->equity);
    }
}
