<?php

namespace Tourze\SubscriptionBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use Tourze\SubscriptionBundle\Entity\Equity;
use Tourze\SubscriptionBundle\Entity\Record;
use Tourze\SubscriptionBundle\Entity\Usage;

/**
 * @internal
 */
#[CoversClass(Usage::class)]
final class UsageTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new Usage();
    }

    /**
     * 提供属性及其样本值的 Data Provider.
     *
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'date' => ['date', new \DateTimeImmutable('2023-05-01')];
        yield 'time' => ['time', '1430'];
        yield 'value' => ['value', '50'];
    }

    public function testCreationWithDefaultValues(): void
    {
        // 测试默认值
        $usage = new Usage();
        $this->assertSame(0, $usage->getId());
        $this->assertNull($usage->getUser());
        $this->assertNull($usage->getRecord());
        $this->assertNull($usage->getEquity());
        $this->assertNull($usage->getDate());
        $this->assertNull($usage->getTime());
        $this->assertNull($usage->getValue());
        $this->assertNull($usage->getCreateTime());
        $this->assertNull($usage->getUpdateTime());
    }

    public function testSetUserWithUserObject(): void
    {
        // 创建模拟对象
        $user = $this->createMock(UserInterface::class);

        $usage = new Usage();
        // 设置用户
        $usage->setUser($user);

        // 验证结果
        $this->assertSame($user, $usage->getUser());
    }

    public function testSetRecordWithRecordObject(): void
    {
        // 创建记录对象
        $record = new Record();

        $usage = new Usage();
        // 设置记录
        $usage->setRecord($record);

        // 验证结果
        $this->assertSame($record, $usage->getRecord());
    }

    public function testSetEquityWithEquityObject(): void
    {
        // 创建权益对象
        $equity = new Equity();
        $equity->setName('测试权益');

        $usage = new Usage();
        // 设置权益
        $usage->setEquity($equity);

        // 验证结果
        $this->assertSame($equity, $usage->getEquity());
    }

    public function testSetValueWithValidString(): void
    {
        $usage = new Usage();
        // 设置值
        $usage->setValue('100');

        // 验证结果
        $this->assertSame('100', $usage->getValue());
    }

    public function testSetDateWithDateTimeObject(): void
    {
        // 创建日期对象
        $date = new \DateTimeImmutable('2023-05-01');

        $usage = new Usage();
        // 设置日期
        $usage->setDate($date);

        // 验证结果
        $this->assertSame($date, $usage->getDate());
    }

    public function testSetTimeWithValidTimeString(): void
    {
        $usage = new Usage();
        // 设置时间
        $usage->setTime('1430');

        // 验证结果
        $this->assertSame('1430', $usage->getTime());
    }
}
