<?php

namespace Tourze\SubscriptionBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use Tourze\SubscriptionBundle\Entity\Equity;
use Tourze\SubscriptionBundle\Entity\Record;
use Tourze\SubscriptionBundle\Entity\Resource;

/**
 * @internal
 */
#[CoversClass(Resource::class)]
final class ResourceTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new Resource();
    }

    /**
     * 提供属性及其样本值的 Data Provider.
     *
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'valid' => ['valid', true];
        yield 'startTime' => ['startTime', new \DateTimeImmutable('2023-01-01')];
        yield 'endTime' => ['endTime', new \DateTimeImmutable('2023-12-31')];
        yield 'value' => ['value', '100'];
    }

    public function testCreationWithDefaultValues(): void
    {
        // 测试默认值
        $resource = new Resource();
        $this->assertSame(0, $resource->getId());
        $this->assertFalse($resource->isValid());
        $this->assertNull($resource->getUser());
        $this->assertNull($resource->getRecord());
        $this->assertNull($resource->getEquity());
        $this->assertNull($resource->getStartTime());
        $this->assertNull($resource->getEndTime());
        $this->assertNull($resource->getValue());
        $this->assertNull($resource->getCreatedBy());
        $this->assertNull($resource->getUpdatedBy());
        $this->assertNull($resource->getCreateTime());
        $this->assertNull($resource->getUpdateTime());
    }

    public function testSetUserWithUserObject(): void
    {
        // 创建模拟对象
        $user = $this->createMock(UserInterface::class);

        $resource = new Resource();
        // 设置用户
        $resource->setUser($user);

        // 验证结果
        $this->assertSame($user, $resource->getUser());
    }

    public function testSetRecordWithRecordObject(): void
    {
        // 创建记录对象
        $record = new Record();

        $resource = new Resource();
        // 设置记录
        $resource->setRecord($record);

        // 验证结果
        $this->assertSame($record, $resource->getRecord());
    }

    public function testSetEquityWithEquityObject(): void
    {
        // 创建权益对象
        $equity = new Equity();
        $equity->setName('测试权益');

        $resource = new Resource();
        // 设置权益
        $resource->setEquity($equity);

        // 验证结果
        $this->assertSame($equity, $resource->getEquity());
    }

    public function testSetValueWithValidString(): void
    {
        $resource = new Resource();
        // 设置值
        $resource->setValue('100');

        // 验证结果
        $this->assertSame('100', $resource->getValue());
    }

    public function testSetStartTimeWithDateTimeObject(): void
    {
        // 创建时间对象
        $time = new \DateTimeImmutable('2023-01-01');

        $resource = new Resource();
        // 设置开始时间
        $resource->setStartTime($time);

        // 验证结果
        $this->assertSame($time, $resource->getStartTime());
    }

    public function testSetEndTimeWithDateTimeObject(): void
    {
        // 创建时间对象
        $time = new \DateTimeImmutable('2023-12-31');

        $resource = new Resource();
        // 设置结束时间
        $resource->setEndTime($time);

        // 验证结果
        $this->assertSame($time, $resource->getEndTime());
    }

    public function testSetEndTimeWithNull(): void
    {
        $resource = new Resource();
        // 设置结束时间为null
        $resource->setEndTime(null);

        // 验证结果
        $this->assertNull($resource->getEndTime());
    }

    public function testIsValidWithDifferentValues(): void
    {
        $resource = new Resource();
        // 测试设置有效
        $resource->setValid(true);
        $this->assertTrue($resource->isValid());

        // 测试设置无效
        $resource->setValid(false);
        $this->assertFalse($resource->isValid());

        // 测试设置为null
        $resource->setValid(null);
        $this->assertNull($resource->isValid());
    }
}
