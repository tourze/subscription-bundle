<?php

namespace SubscriptionBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use SubscriptionBundle\Entity\Equity;
use SubscriptionBundle\Entity\Record;
use SubscriptionBundle\Entity\Resource;
use Symfony\Component\Security\Core\User\UserInterface;

class ResourceTest extends TestCase
{
    private Resource $resource;

    protected function setUp(): void
    {
        $this->resource = new Resource();
    }

    public function testGettersAndSetters_withValidData(): void
    {
        // 创建模拟对象
        $user = $this->createMock(UserInterface::class);
        $record = new Record();
        $equity = new Equity();
        $equity->setName('测试权益');

        // 设置基本属性
        $startTime = new \DateTimeImmutable('2023-01-01');
        $endTime = new \DateTimeImmutable('2023-12-31');
        $now = new \DateTimeImmutable();

        $this->resource->setUser($user);
        $this->resource->setRecord($record);
        $this->resource->setEquity($equity);
        $this->resource->setStartTime($startTime);
        $this->resource->setEndTime($endTime);
        $this->resource->setValue('1000');
        $this->resource->setValid(true);
        $this->resource->setCreatedBy('admin');
        $this->resource->setUpdatedBy('admin');
        $this->resource->setCreateTime($now);
        $this->resource->setUpdateTime($now);

        // 验证结果
        $this->assertSame($user, $this->resource->getUser());
        $this->assertSame($record, $this->resource->getRecord());
        $this->assertSame($equity, $this->resource->getEquity());
        $this->assertSame($startTime, $this->resource->getStartTime());
        $this->assertSame($endTime, $this->resource->getEndTime());
        $this->assertSame('1000', $this->resource->getValue());
        $this->assertTrue($this->resource->isValid());
        $this->assertSame('admin', $this->resource->getCreatedBy());
        $this->assertSame('admin', $this->resource->getUpdatedBy());
        $this->assertSame($now, $this->resource->getCreateTime());
        $this->assertSame($now, $this->resource->getUpdateTime());
    }

    public function testCreation_withDefaultValues(): void
    {
        // 测试默认值
        $this->assertSame(0, $this->resource->getId());
        $this->assertFalse($this->resource->isValid());
        $this->assertNull($this->resource->getUser());
        $this->assertNull($this->resource->getRecord());
        $this->assertNull($this->resource->getEquity());
        $this->assertNull($this->resource->getStartTime());
        $this->assertNull($this->resource->getEndTime());
        $this->assertNull($this->resource->getValue());
        $this->assertNull($this->resource->getCreatedBy());
        $this->assertNull($this->resource->getUpdatedBy());
        $this->assertNull($this->resource->getCreateTime());
        $this->assertNull($this->resource->getUpdateTime());
    }

    public function testSetUser_withUserObject(): void
    {
        // 创建模拟对象
        $user = $this->createMock(UserInterface::class);

        // 设置用户
        $result = $this->resource->setUser($user);

        // 验证结果
        $this->assertSame($this->resource, $result); // 返回自身以支持链式调用
        $this->assertSame($user, $this->resource->getUser());
    }

    public function testSetRecord_withRecordObject(): void
    {
        // 创建记录对象
        $record = new Record();

        // 设置记录
        $result = $this->resource->setRecord($record);

        // 验证结果
        $this->assertSame($this->resource, $result); // 返回自身以支持链式调用
        $this->assertSame($record, $this->resource->getRecord());
    }

    public function testSetEquity_withEquityObject(): void
    {
        // 创建权益对象
        $equity = new Equity();
        $equity->setName('测试权益');

        // 设置权益
        $result = $this->resource->setEquity($equity);

        // 验证结果
        $this->assertSame($this->resource, $result); // 返回自身以支持链式调用
        $this->assertSame($equity, $this->resource->getEquity());
    }

    public function testSetValue_withValidString(): void
    {
        // 设置值
        $result = $this->resource->setValue('100');

        // 验证结果
        $this->assertSame($this->resource, $result); // 返回自身以支持链式调用
        $this->assertSame('100', $this->resource->getValue());
    }

    public function testSetStartTime_withDateTimeObject(): void
    {
        // 创建时间对象
        $time = new \DateTimeImmutable('2023-01-01');

        // 设置开始时间
        $result = $this->resource->setStartTime($time);

        // 验证结果
        $this->assertSame($this->resource, $result); // 返回自身以支持链式调用
        $this->assertSame($time, $this->resource->getStartTime());
    }

    public function testSetEndTime_withDateTimeObject(): void
    {
        // 创建时间对象
        $time = new \DateTimeImmutable('2023-12-31');

        // 设置结束时间
        $result = $this->resource->setEndTime($time);

        // 验证结果
        $this->assertSame($this->resource, $result); // 返回自身以支持链式调用
        $this->assertSame($time, $this->resource->getEndTime());
    }

    public function testSetEndTime_withNull(): void
    {
        // 设置结束时间为null
        $result = $this->resource->setEndTime(null);

        // 验证结果
        $this->assertSame($this->resource, $result); // 返回自身以支持链式调用
        $this->assertNull($this->resource->getEndTime());
    }

    public function testIsValid_withDifferentValues(): void
    {
        // 测试设置有效
        $this->resource->setValid(true);
        $this->assertTrue($this->resource->isValid());

        // 测试设置无效
        $this->resource->setValid(false);
        $this->assertFalse($this->resource->isValid());

        // 测试设置为null
        $this->resource->setValid(null);
        $this->assertNull($this->resource->isValid());
    }
}
