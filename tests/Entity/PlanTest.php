<?php

namespace SubscriptionBundle\Tests\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use SubscriptionBundle\Entity\Equity;
use SubscriptionBundle\Entity\Plan;
use SubscriptionBundle\Entity\Record;

class PlanTest extends TestCase
{
    private Plan $plan;

    protected function setUp(): void
    {
        $this->plan = new Plan();
    }

    public function testGettersAndSetters_withValidData(): void
    {
        // 测试基本属性
        $this->plan->setName('测试计划');
        $this->plan->setDescription('这是一个测试计划');
        $this->plan->setPeriodDay(30);
        $this->plan->setRenewCount(3);
        $this->plan->setValid(true);
        $this->plan->setCreatedBy('admin');
        $this->plan->setUpdatedBy('admin');

        $now = new \DateTimeImmutable();
        $this->plan->setCreateTime($now);
        $this->plan->setUpdateTime($now);

        // 验证结果
        $this->assertSame('测试计划', $this->plan->getName());
        $this->assertSame('这是一个测试计划', $this->plan->getDescription());
        $this->assertSame(30, $this->plan->getPeriodDay());
        $this->assertSame(3, $this->plan->getRenewCount());
        $this->assertTrue($this->plan->isValid());
        $this->assertSame('admin', $this->plan->getCreatedBy());
        $this->assertSame('admin', $this->plan->getUpdatedBy());
        $this->assertSame($now, $this->plan->getCreateTime());
        $this->assertSame($now, $this->plan->getUpdateTime());
    }

    public function testCreation_withDefaultValues(): void
    {
        // 测试默认值
        $this->assertSame(0, $this->plan->getId());
        $this->assertFalse($this->plan->isValid());
        $this->assertNull($this->plan->getName());
        $this->assertNull($this->plan->getDescription());
        $this->assertSame(30, $this->plan->getPeriodDay());
        $this->assertSame(0, $this->plan->getRenewCount());
        $this->assertNull($this->plan->getCreatedBy());
        $this->assertNull($this->plan->getUpdatedBy());
        $this->assertNull($this->plan->getCreateTime());
        $this->assertNull($this->plan->getUpdateTime());
        $this->assertInstanceOf(ArrayCollection::class, $this->plan->getEquities());
        $this->assertCount(0, $this->plan->getEquities());
        $this->assertInstanceOf(ArrayCollection::class, $this->plan->getRecords());
        $this->assertCount(0, $this->plan->getRecords());
    }

    public function testAddEquity_withValidEquity(): void
    {
        $equity = new Equity();
        $equity->setName('测试权益');
        
        // 添加权益
        $result = $this->plan->addEquity($equity);
        
        // 验证结果
        $this->assertSame($this->plan, $result); // 返回自身以支持链式调用
        $this->assertCount(1, $this->plan->getEquities());
        $this->assertTrue($this->plan->getEquities()->contains($equity));
    }

    public function testRemoveEquity_withExistingEquity(): void
    {
        // 准备测试数据
        $equity = new Equity();
        $equity->setName('测试权益');
        $this->plan->addEquity($equity);
        
        // 移除权益
        $result = $this->plan->removeEquity($equity);
        
        // 验证结果
        $this->assertSame($this->plan, $result); // 返回自身以支持链式调用
        $this->assertCount(0, $this->plan->getEquities());
        $this->assertFalse($this->plan->getEquities()->contains($equity));
    }

    public function testAddRecord_withValidRecord(): void
    {
        // 准备测试数据
        $record = new Record();
        
        // 添加记录
        $result = $this->plan->addRecord($record);
        
        // 验证结果
        $this->assertSame($this->plan, $result); // 返回自身以支持链式调用
        $this->assertCount(1, $this->plan->getRecords());
        $this->assertTrue($this->plan->getRecords()->contains($record));
        $this->assertSame($this->plan, $record->getPlan()); // 验证双向关系
    }

    public function testRemoveRecord_withExistingRecord(): void
    {
        // 准备测试数据
        $record = new Record();
        $this->plan->addRecord($record);
        
        // 移除记录
        $result = $this->plan->removeRecord($record);
        
        // 验证结果
        $this->assertSame($this->plan, $result); // 返回自身以支持链式调用
        $this->assertCount(0, $this->plan->getRecords());
        $this->assertFalse($this->plan->getRecords()->contains($record));
        $this->assertNull($record->getPlan()); // 验证双向关系移除
    }

    public function testIsValid_withTrueValue(): void
    {
        $this->plan->setValid(true);
        $this->assertTrue($this->plan->isValid());
    }

    public function testIsValid_withFalseValue(): void
    {
        $this->plan->setValid(false);
        $this->assertFalse($this->plan->isValid());
    }

    public function testIsValid_withNullValue(): void
    {
        $this->plan->setValid(null);
        $this->assertNull($this->plan->isValid());
    }
} 