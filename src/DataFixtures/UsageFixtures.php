<?php

namespace Tourze\SubscriptionBundle\DataFixtures;

use BizUserBundle\Entity\BizUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use Tourze\SubscriptionBundle\Entity\Equity;
use Tourze\SubscriptionBundle\Entity\Record;
use Tourze\SubscriptionBundle\Entity\Usage;

#[When(env: 'test')]
#[When(env: 'dev')]
class UsageFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    public const USAGE_LOTTERY_TODAY = 'usage-lottery-today';
    public const USAGE_TRAFFIC_TODAY = 'usage-traffic-today';

    public static function getGroups(): array
    {
        return ['subscription', 'test'];
    }

    public function getDependencies(): array
    {
        return [
            EquityFixtures::class,
            RecordFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        // 获取引用的实体
        $equity = $this->getReference(EquityFixtures::EQUITY_LOTTERY, Equity::class);
        $record = $this->getReference(RecordFixtures::RECORD_BASIC, Record::class);
        $user = $this->getReference(RecordFixtures::TEST_USER, BizUser::class);

        // 创建第一个 Usage (今日抽奖次数)
        $usage1 = new Usage();
        $usage1->setUser($user);
        $usage1->setRecord($record);
        $usage1->setEquity($equity);
        $usage1->setDate(new \DateTimeImmutable('2024-01-01'));
        $usage1->setTime('1200');
        $usage1->setValue('1');
        $manager->persist($usage1);

        // 创建第二个 Usage (今日流量)
        $trafficEquity = $this->getReference(EquityFixtures::EQUITY_TRAFFIC, Equity::class);
        $usage2 = new Usage();
        $usage2->setUser($user);
        $usage2->setRecord($record);
        $usage2->setEquity($trafficEquity);
        $usage2->setDate(new \DateTimeImmutable('2024-01-01'));
        $usage2->setTime('1400');
        $usage2->setValue('1048576');
        $manager->persist($usage2);

        // 创建第三个 Usage (次日抽奖次数)
        $usage3 = new Usage();
        $usage3->setUser($user);
        $usage3->setRecord($record);
        $usage3->setEquity($equity);
        $usage3->setDate(new \DateTimeImmutable('2024-01-02'));
        $usage3->setTime('1200');
        $usage3->setValue('2');
        $manager->persist($usage3);

        // 添加引用
        $this->addReference(self::USAGE_LOTTERY_TODAY, $usage1);
        $this->addReference(self::USAGE_TRAFFIC_TODAY, $usage2);

        // 确保数据写入数据库
        $manager->flush();
    }
}
