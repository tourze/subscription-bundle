<?php

namespace Tourze\SubscriptionBundle\Tests\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use Tourze\SubscriptionBundle\Entity\Equity;
use Tourze\SubscriptionBundle\Entity\Plan;
use Tourze\SubscriptionBundle\Entity\Record;

/**
 * @internal
 */
#[CoversClass(Plan::class)]
final class PlanTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new Plan();
    }

    /**
     * 提供属性及其样本值的 Data Provider.
     *
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'valid' => ['valid', true];
        yield 'name' => ['name', '测试计划'];
        yield 'description' => ['description', '这是一个测试计划'];
        yield 'periodDay' => ['periodDay', 30];
        yield 'renewCount' => ['renewCount', 5];
    }

    public function testCreationWithDefaultValues(): void
    {
        // 测试默认值
        $plan = new Plan();
        $this->assertSame(0, $plan->getId());
        $this->assertFalse($plan->isValid());
        $this->assertNull($plan->getName());
        $this->assertNull($plan->getDescription());
        $this->assertSame(30, $plan->getPeriodDay());
        $this->assertSame(0, $plan->getRenewCount());
        $this->assertNull($plan->getCreatedBy());
        $this->assertNull($plan->getUpdatedBy());
        $this->assertNull($plan->getCreateTime());
        $this->assertNull($plan->getUpdateTime());
        $this->assertInstanceOf(ArrayCollection::class, $plan->getEquities());
        $this->assertCount(0, $plan->getEquities());
        $this->assertInstanceOf(ArrayCollection::class, $plan->getRecords());
        $this->assertCount(0, $plan->getRecords());
    }

    public function testAddEquityWithValidEquity(): void
    {
        $equity = new Equity();
        $equity->setName('测试权益');

        $plan = new Plan();
        // 添加权益
        $plan->addEquity($equity);

        // 验证结果
        $this->assertCount(1, $plan->getEquities());
        $this->assertTrue($plan->getEquities()->contains($equity));
    }

    public function testRemoveEquityWithExistingEquity(): void
    {
        // 准备测试数据
        $equity = new Equity();
        $equity->setName('测试权益');
        $plan = new Plan();
        $plan->addEquity($equity);

        // 移除权益
        $plan->removeEquity($equity);

        // 验证结果
        $this->assertCount(0, $plan->getEquities());
        $this->assertFalse($plan->getEquities()->contains($equity));
    }

    public function testAddRecordWithValidRecord(): void
    {
        // 准备测试数据
        $record = new Record();

        $plan = new Plan();
        // 添加记录
        $plan->addRecord($record);

        // 验证结果
        $this->assertCount(1, $plan->getRecords());
        $this->assertTrue($plan->getRecords()->contains($record));
        $this->assertSame($plan, $record->getPlan()); // 验证双向关系
    }

    public function testRemoveRecordWithExistingRecord(): void
    {
        // 准备测试数据
        $record = new Record();
        $plan = new Plan();
        $plan->addRecord($record);

        // 移除记录
        $plan->removeRecord($record);

        // 验证结果
        $this->assertCount(0, $plan->getRecords());
        $this->assertFalse($plan->getRecords()->contains($record));
        $this->assertNull($record->getPlan()); // 验证双向关系移除
    }

    public function testIsValidWithTrueValue(): void
    {
        $plan = new Plan();
        $plan->setValid(true);
        $this->assertTrue($plan->isValid());
    }

    public function testIsValidWithFalseValue(): void
    {
        $plan = new Plan();
        $plan->setValid(false);
        $this->assertFalse($plan->isValid());
    }

    public function testIsValidWithNullValue(): void
    {
        $plan = new Plan();
        $plan->setValid(null);
        $this->assertNull($plan->isValid());
    }
}
