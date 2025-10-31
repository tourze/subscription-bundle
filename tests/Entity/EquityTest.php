<?php

namespace Tourze\SubscriptionBundle\Tests\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use Tourze\SubscriptionBundle\Entity\Equity;
use Tourze\SubscriptionBundle\Entity\Plan;

/**
 * @internal
 */
#[CoversClass(Equity::class)]
final class EquityTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new Equity();
    }

    /**
     * 提供属性及其样本值的 Data Provider.
     *
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'name' => ['name', '测试权益'];
        yield 'type' => ['type', 'COUNT'];
        yield 'value' => ['value', '100'];
        yield 'description' => ['description', '这是一个测试权益'];
    }

    public function testCreationWithDefaultValues(): void
    {
        // 测试默认值
        $equity = new Equity();
        $this->assertSame(0, $equity->getId());
        $this->assertNull($equity->getDescription());
        $this->assertSame('0', $equity->getValue());
        $this->assertNull($equity->getCreatedBy());
        $this->assertNull($equity->getUpdatedBy());
        $this->assertNull($equity->getCreateTime());
        $this->assertNull($equity->getUpdateTime());
        $this->assertInstanceOf(ArrayCollection::class, $equity->getPlans());
        $this->assertCount(0, $equity->getPlans());
    }

    public function testAddPlanWithValidPlan(): void
    {
        // 准备测试数据
        $plan = new Plan();
        $plan->setName('测试计划');

        $equity = new Equity();
        // 添加计划
        $equity->addPlan($plan);

        // 验证结果
        $this->assertCount(1, $equity->getPlans());
        $this->assertTrue($equity->getPlans()->contains($plan));
    }

    public function testAddPlanWithDuplicatePlan(): void
    {
        // 准备测试数据
        $plan = new Plan();
        $plan->setName('测试计划');

        $equity = new Equity();
        // 添加两次相同的计划
        $equity->addPlan($plan);
        $equity->addPlan($plan);

        // 验证结果 - 应该只添加一次
        $this->assertCount(1, $equity->getPlans());
    }

    public function testRemovePlanWithExistingPlan(): void
    {
        // 准备测试数据
        $plan = new Plan();
        $plan->setName('测试计划');
        $equity = new Equity();
        $equity->addPlan($plan);

        // 移除计划
        $equity->removePlan($plan);

        // 验证结果
        $this->assertCount(0, $equity->getPlans());
        $this->assertFalse($equity->getPlans()->contains($plan));
    }

    public function testRemovePlanWithNonExistingPlan(): void
    {
        // 准备测试数据
        $plan = new Plan();
        $plan->setName('测试计划');

        $equity = new Equity();
        // 移除未添加的计划
        $equity->removePlan($plan);

        // 验证结果 - 不应抛出异常，而是静默失败
        $this->assertCount(0, $equity->getPlans());
    }

    public function testToStringWithValidId(): void
    {
        // 使用反射设置私有属性
        $reflectionClass = new \ReflectionClass(Equity::class);
        $idProperty = $reflectionClass->getProperty('id');
        $idProperty->setAccessible(true);
        $equity = new Equity();
        $idProperty->setValue($equity, 1);

        $equity->setName('测试权益');

        // 测试__toString方法
        $this->assertEquals('测试权益', (string) $equity);
    }

    public function testToStringWithoutId(): void
    {
        $equity = new Equity();
        $equity->setName('测试权益');

        // ID为0时应返回'new Equity'
        $this->assertEquals('new Equity', (string) $equity);
    }
}
