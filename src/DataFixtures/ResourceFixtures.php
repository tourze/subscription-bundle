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
use Tourze\SubscriptionBundle\Entity\Resource;

#[When(env: 'test')]
#[When(env: 'dev')]
class ResourceFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    public const RESOURCE_LOTTERY = 'resource-lottery';
    public const RESOURCE_TRAFFIC = 'resource-traffic';

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

        // 创建第一个 Resource (抽奖次数)
        $resource1 = new Resource();
        $resource1->setUser($user);
        $resource1->setRecord($record);
        $resource1->setEquity($equity);
        $resource1->setValid(true);
        $resource1->setStartTime(new \DateTimeImmutable('2024-01-01'));
        $resource1->setEndTime(new \DateTimeImmutable('2024-12-31'));
        $resource1->setValue('10');
        $manager->persist($resource1);

        // 创建第二个 Resource (流量包)
        $trafficEquity = $this->getReference(EquityFixtures::EQUITY_TRAFFIC, Equity::class);
        $resource2 = new Resource();
        $resource2->setUser($user);
        $resource2->setRecord($record);
        $resource2->setEquity($trafficEquity);
        $resource2->setValid(true);
        $resource2->setStartTime(new \DateTimeImmutable('2024-01-01'));
        $resource2->setEndTime(new \DateTimeImmutable('2024-12-31'));
        $resource2->setValue('107374182400');
        $manager->persist($resource2);

        // 添加引用
        $this->addReference(self::RESOURCE_LOTTERY, $resource1);
        $this->addReference(self::RESOURCE_TRAFFIC, $resource2);

        // 确保数据写入数据库
        $manager->flush();
    }
}
