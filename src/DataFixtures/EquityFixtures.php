<?php

namespace Tourze\SubscriptionBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use Tourze\SubscriptionBundle\Entity\Equity;

#[When(env: 'test')]
#[When(env: 'dev')]
final class EquityFixtures extends Fixture implements FixtureGroupInterface
{
    public const EQUITY_LOTTERY = 'equity-lottery';
    public const EQUITY_TRAFFIC = 'equity-traffic';
    public const EQUITY_STORAGE = 'equity-storage';
    public const EQUITY_BANDWIDTH = 'equity-bandwidth';

    public static function getGroups(): array
    {
        return ['subscription', 'test'];
    }

    public function load(ObjectManager $manager): void
    {
        $lotteryEquity = new Equity();
        $lotteryEquity->setName('抽奖次数');
        $lotteryEquity->setType('lottery');
        $lotteryEquity->setValue('10');
        $lotteryEquity->setDescription('每月可参与抽奖的次数');

        $manager->persist($lotteryEquity);

        $trafficEquity = new Equity();
        $trafficEquity->setName('流量包');
        $trafficEquity->setType('traffic');
        $trafficEquity->setValue('107374182400');
        $trafficEquity->setDescription('100GB月流量包');

        $manager->persist($trafficEquity);

        $storageEquity = new Equity();
        $storageEquity->setName('存储空间');
        $storageEquity->setType('storage');
        $storageEquity->setValue('536870912000');
        $storageEquity->setDescription('500GB云存储空间');

        $manager->persist($storageEquity);

        $bandwidthEquity = new Equity();
        $bandwidthEquity->setName('带宽');
        $bandwidthEquity->setType('bandwidth');
        $bandwidthEquity->setValue('1048576');
        $bandwidthEquity->setDescription('1Mbps专享带宽');

        $manager->persist($bandwidthEquity);

        $manager->flush();

        $this->addReference(self::EQUITY_LOTTERY, $lotteryEquity);
        $this->addReference(self::EQUITY_TRAFFIC, $trafficEquity);
        $this->addReference(self::EQUITY_STORAGE, $storageEquity);
        $this->addReference(self::EQUITY_BANDWIDTH, $bandwidthEquity);
    }
}
