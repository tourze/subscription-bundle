<?php

namespace Tourze\SubscriptionBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use Tourze\SubscriptionBundle\Entity\Equity;
use Tourze\SubscriptionBundle\Entity\Record;
use Tourze\SubscriptionBundle\Entity\Usage;
use Symfony\Component\Security\Core\User\UserInterface;

class UsageTest extends TestCase
{
    private Usage $usage;

    protected function setUp(): void
    {
        $this->usage = new Usage();
    }

    public function testGettersAndSetters_withValidData(): void
    {
        // 创建模拟对象
        $user = $this->createMock(UserInterface::class);
        $record = new Record();
        $equity = new Equity();
        $equity->setName('测试权益');

        // 设置基本属性
        $date = new \DateTimeImmutable('2023-05-01');
        $time = '1430'; // 14:30
        $now = new \DateTimeImmutable();

        $this->usage->setUser($user);
        $this->usage->setRecord($record);
        $this->usage->setEquity($equity);
        $this->usage->setDate($date);
        $this->usage->setTime($time);
        $this->usage->setValue('10');
        $this->usage->setCreateTime($now);
        $this->usage->setUpdateTime($now);

        // 验证结果
        $this->assertSame($user, $this->usage->getUser());
        $this->assertSame($record, $this->usage->getRecord());
        $this->assertSame($equity, $this->usage->getEquity());
        $this->assertSame($date, $this->usage->getDate());
        $this->assertSame($time, $this->usage->getTime());
        $this->assertSame('10', $this->usage->getValue());
        $this->assertSame($now, $this->usage->getCreateTime());
        $this->assertSame($now, $this->usage->getUpdateTime());
    }

    public function testCreation_withDefaultValues(): void
    {
        // 测试默认值
        $this->assertSame(0, $this->usage->getId());
        $this->assertNull($this->usage->getUser());
        $this->assertNull($this->usage->getRecord());
        $this->assertNull($this->usage->getEquity());
        $this->assertNull($this->usage->getDate());
        $this->assertNull($this->usage->getTime());
        $this->assertNull($this->usage->getValue());
        $this->assertNull($this->usage->getCreateTime());
        $this->assertNull($this->usage->getUpdateTime());
    }

    public function testSetUser_withUserObject(): void
    {
        // 创建模拟对象
        $user = $this->createMock(UserInterface::class);

        // 设置用户
        $result = $this->usage->setUser($user);

        // 验证结果
        $this->assertSame($this->usage, $result); // 返回自身以支持链式调用
        $this->assertSame($user, $this->usage->getUser());
    }

    public function testSetRecord_withRecordObject(): void
    {
        // 创建记录对象
        $record = new Record();

        // 设置记录
        $result = $this->usage->setRecord($record);

        // 验证结果
        $this->assertSame($this->usage, $result); // 返回自身以支持链式调用
        $this->assertSame($record, $this->usage->getRecord());
    }

    public function testSetEquity_withEquityObject(): void
    {
        // 创建权益对象
        $equity = new Equity();
        $equity->setName('测试权益');

        // 设置权益
        $result = $this->usage->setEquity($equity);

        // 验证结果
        $this->assertSame($this->usage, $result); // 返回自身以支持链式调用
        $this->assertSame($equity, $this->usage->getEquity());
    }

    public function testSetValue_withValidString(): void
    {
        // 设置值
        $result = $this->usage->setValue('100');

        // 验证结果
        $this->assertSame($this->usage, $result); // 返回自身以支持链式调用
        $this->assertSame('100', $this->usage->getValue());
    }

    public function testSetDate_withDateTimeObject(): void
    {
        // 创建日期对象
        $date = new \DateTimeImmutable('2023-05-01');

        // 设置日期
        $result = $this->usage->setDate($date);

        // 验证结果
        $this->assertSame($this->usage, $result); // 返回自身以支持链式调用
        $this->assertSame($date, $this->usage->getDate());
    }

    public function testSetTime_withValidTimeString(): void
    {
        // 设置时间
        $result = $this->usage->setTime('1430');

        // 验证结果
        $this->assertSame($this->usage, $result); // 返回自身以支持链式调用
        $this->assertSame('1430', $this->usage->getTime());
    }
}
