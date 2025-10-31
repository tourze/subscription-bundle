<?php

namespace Tourze\SubscriptionBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;
use Tourze\SubscriptionBundle\Enum\SubscribeStatus;

/**
 * @internal
 */
#[CoversClass(SubscribeStatus::class)]
final class SubscribeStatusTest extends AbstractEnumTestCase
{
    public function testCasesWithAllEnums(): void
    {
        // 测试枚举数量
        $cases = SubscribeStatus::cases();
        $this->assertCount(2, $cases);
        $this->assertContains(SubscribeStatus::ACTIVE, $cases);
        $this->assertContains(SubscribeStatus::EXPIRED, $cases);
    }

    #[TestWith([SubscribeStatus::ACTIVE, 'active', '活跃'])]
    #[TestWith([SubscribeStatus::EXPIRED, 'expiry', '过期'])]
    public function testValueAndLabel(SubscribeStatus $enum, string $expectedValue, string $expectedLabel): void
    {
        $this->assertSame($expectedValue, $enum->value);
        $this->assertSame($expectedLabel, $enum->getLabel());
    }

    #[TestWith([SubscribeStatus::ACTIVE, 'success'])]
    #[TestWith([SubscribeStatus::EXPIRED, 'danger'])]
    public function testGetBadge(SubscribeStatus $enum, string $expectedBadge): void
    {
        $this->assertSame($expectedBadge, $enum->getBadge());
    }

    public function testValueUniqueness(): void
    {
        $values = [];
        foreach (SubscribeStatus::cases() as $case) {
            $this->assertNotContains($case->value, $values, "Value '{$case->value}' is not unique");
            $values[] = $case->value;
        }
    }

    public function testLabelUniqueness(): void
    {
        $labels = [];
        foreach (SubscribeStatus::cases() as $case) {
            $label = $case->getLabel();
            $this->assertNotContains($label, $labels, "Label '{$label}' is not unique");
            $labels[] = $label;
        }
    }

    #[TestWith(['active', SubscribeStatus::ACTIVE])]
    #[TestWith(['expiry', SubscribeStatus::EXPIRED])]
    public function testFromWithValidValues(string $value, SubscribeStatus $expected): void
    {
        $this->assertSame($expected, SubscribeStatus::from($value));
    }

    #[TestWith(['invalid'])]
    #[TestWith([''])]
    #[TestWith(['null'])]
    public function testFromWithInvalidValues(string $invalidValue): void
    {
        $this->expectException(\ValueError::class);
        SubscribeStatus::from($invalidValue);
    }

    #[TestWith(['active', SubscribeStatus::ACTIVE])]
    #[TestWith(['expiry', SubscribeStatus::EXPIRED])]
    public function testTryFromWithValidValues(string $value, SubscribeStatus $expected): void
    {
        $this->assertSame($expected, SubscribeStatus::tryFrom($value));
    }

    public function testToArray(): void
    {
        // 测试 ACTIVE 枚举转数组
        $activeArray = SubscribeStatus::ACTIVE->toArray();
        $expectedActive = [
            'value' => 'active',
            'label' => '活跃',
        ];
        $this->assertSame($expectedActive, $activeArray);

        // 测试 EXPIRED 枚举转数组
        $expiredArray = SubscribeStatus::EXPIRED->toArray();
        $expectedExpired = [
            'value' => 'expiry',
            'label' => '过期',
        ];
        $this->assertSame($expectedExpired, $expiredArray);
    }
}
