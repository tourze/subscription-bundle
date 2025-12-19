<?php

declare(strict_types=1);

namespace Tourze\SubscriptionBundle\DataFixtures;

use BizUserBundle\Entity\BizUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\SubscriptionBundle\Entity\Plan;
use Tourze\SubscriptionBundle\Entity\Record;
use Tourze\SubscriptionBundle\Enum\SubscribeStatus;
use Tourze\SubscriptionBundle\Tests\Fixtures\TestUser;

#[When(env: 'dev')]
final class RecordFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    public const RECORD_BASIC = 'record-basic';
    public const TEST_USER = 'test-user';

    public static function getGroups(): array
    {
        return ['subscription'];
    }

    public function getDependencies(): array
    {
        return [
            PlanFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        // 使用引用获取 Plan
        $plan = $this->getReference(PlanFixtures::PLAN_BASIC, Plan::class);

        // 检查 BizUser 类是否存在，仅在测试环境需要时创建
        $user = null;
        if (class_exists('BizUserBundle\Entity\BizUser')) {
            // 创建一个可持久化的测试用户
            $user = new BizUser();
            $user->setUsername('test_user_for_subscription');
            $user->setEmail('test@subscription.com');
            $user->setPasswordHash(password_hash('test123', PASSWORD_DEFAULT));
            $user->setCreateTime(new \DateTimeImmutable());
            $user->setUpdateTime(new \DateTimeImmutable());
            $manager->persist($user);
        } else {
            // 使用测试用户
            $user = new TestUser(1, 'test_user_for_subscription', 'test@subscription.com');
        }

        // 创建 Record
        $record = new Record();
        $record->setUser($user);
        $record->setPlan($plan);
        $record->setStatus(SubscribeStatus::ACTIVE);
        $record->setValid(true);
        $record->setActiveTime(new \DateTimeImmutable('2024-01-01'));
        $record->setExpireTime(new \DateTimeImmutable('2024-12-31'));
        $record->setCreateTime(new \DateTimeImmutable());
        $record->setUpdateTime(new \DateTimeImmutable());
        $manager->persist($record);

        // 添加额外的测试数据
        for ($i = 2; $i <= 5; ++$i) {
            $additionalUser = null;
            if (class_exists('BizUserBundle\Entity\BizUser')) {
                $additionalUser = new BizUser();
                $additionalUser->setUsername("test_user_{$i}");
                $additionalUser->setEmail("test{$i}@subscription.com");
                $additionalUser->setPasswordHash(password_hash('test123', PASSWORD_DEFAULT));
                $additionalUser->setCreateTime(new \DateTimeImmutable());
                $additionalUser->setUpdateTime(new \DateTimeImmutable());
                $manager->persist($additionalUser);
            } else {
                $additionalUser = new TestUser($i, "test_user_{$i}", "test{$i}@subscription.com");
            }

            $additionalRecord = new Record();
            $additionalRecord->setUser($additionalUser);
            $additionalRecord->setPlan($plan);
            $additionalRecord->setStatus(0 === $i % 2 ? SubscribeStatus::ACTIVE : SubscribeStatus::EXPIRED);
            $additionalRecord->setValid(0 === $i % 2);
            $additionalRecord->setActiveTime(new \DateTimeImmutable('2024-01-' . str_pad((string) $i, 2, '0', STR_PAD_LEFT)));
            $additionalRecord->setExpireTime(new \DateTimeImmutable('2024-12-' . str_pad((string) $i, 2, '0', STR_PAD_LEFT)));
            $additionalRecord->setCreateTime(new \DateTimeImmutable());
            $additionalRecord->setUpdateTime(new \DateTimeImmutable());
            $manager->persist($additionalRecord);
        }

        $manager->flush();

        // 添加引用
        $this->addReference(self::RECORD_BASIC, $record);
        if ($user !== null) {
            $this->addReference(self::TEST_USER, $user);
        }
    }
}
