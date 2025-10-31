<?php

namespace Tourze\SubscriptionBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use Tourze\SubscriptionBundle\Entity\Plan;
use Tourze\SubscriptionBundle\Entity\Record;
use Tourze\SubscriptionBundle\Enum\SubscribeStatus;

/**
 * @internal
 */
#[CoversClass(Record::class)]
final class RecordTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new Record();
    }

    /**
     * 提供属性及其样本值的 Data Provider.
     *
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'valid' => ['valid', true];
        yield 'activeTime' => ['activeTime', new \DateTimeImmutable('2023-01-01')];
        yield 'expireTime' => ['expireTime', new \DateTimeImmutable('2023-12-31')];
        yield 'status' => ['status', SubscribeStatus::ACTIVE];
    }

    public function testCreationWithDefaultValues(): void
    {
        // 测试默认值
        $record = new Record();
        $this->assertSame(0, $record->getId());
        $this->assertFalse($record->isValid());
        $this->assertNull($record->getPlan());
        $this->assertNull($record->getUser());
        $this->assertNull($record->getActiveTime());
        $this->assertNull($record->getExpireTime());
        $this->assertNull($record->getStatus());
        $this->assertNull($record->getCreatedBy());
        $this->assertNull($record->getUpdatedBy());
        $this->assertNull($record->getCreateTime());
        $this->assertNull($record->getUpdateTime());
    }

    public function testSetStatusWithValidEnum(): void
    {
        $record = new Record();
        // 设置激活状态
        $record->setStatus(SubscribeStatus::ACTIVE);
        $this->assertSame(SubscribeStatus::ACTIVE, $record->getStatus());

        // 设置过期状态
        $record->setStatus(SubscribeStatus::EXPIRED);
        $this->assertSame(SubscribeStatus::EXPIRED, $record->getStatus());

        // 设置空状态
        $record->setStatus(null);
        $this->assertNull($record->getStatus());
    }

    public function testIsValidWithDifferentValues(): void
    {
        $record = new Record();
        // 测试设置有效
        $record->setValid(true);
        $this->assertTrue($record->isValid());

        // 测试设置无效
        $record->setValid(false);
        $this->assertFalse($record->isValid());

        // 测试设置为null
        $record->setValid(null);
        $this->assertNull($record->isValid());
    }

    public function testSetPlanWithPlanObject(): void
    {
        // 创建计划对象
        $plan = new Plan();
        $plan->setName('测试计划');

        $record = new Record();
        // 设置计划
        $record->setPlan($plan);

        // 验证结果
        $this->assertSame($plan, $record->getPlan());
    }

    public function testSetPlanWithNull(): void
    {
        $record = new Record();
        // 设置为null
        $record->setPlan(null);

        // 验证结果
        $this->assertNull($record->getPlan());
    }
}
