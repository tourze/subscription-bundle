<?php

namespace Tourze\SubscriptionBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use Tourze\SubscriptionBundle\Entity\Equity;
use Tourze\SubscriptionBundle\Entity\Plan;

#[When(env: 'test')]
#[When(env: 'dev')]
final class PlanFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    public const PLAN_BASIC = 'plan-basic';
    public const PLAN_PREMIUM = 'plan-premium';
    public const PLAN_ENTERPRISE = 'plan-enterprise';

    public static function getGroups(): array
    {
        return ['subscription', 'test'];
    }

    public function load(ObjectManager $manager): void
    {
        $lotteryEquity = $this->getReference(EquityFixtures::EQUITY_LOTTERY, Equity::class);
        $trafficEquity = $this->getReference(EquityFixtures::EQUITY_TRAFFIC, Equity::class);
        $storageEquity = $this->getReference(EquityFixtures::EQUITY_STORAGE, Equity::class);
        $bandwidthEquity = $this->getReference(EquityFixtures::EQUITY_BANDWIDTH, Equity::class);

        $basicPlan = new Plan();
        $basicPlan->setName('基础版');
        $basicPlan->setDescription('适合个人用户的基础版本');
        $basicPlan->setPeriodDay(30);
        $basicPlan->setValid(true);
        $basicPlan->addEquity($lotteryEquity);
        $basicPlan->addEquity($trafficEquity);

        $manager->persist($basicPlan);

        $premiumPlan = new Plan();
        $premiumPlan->setName('高级版');
        $premiumPlan->setDescription('适合中小企业的高级版本');
        $premiumPlan->setPeriodDay(60);
        $premiumPlan->setValid(true);
        $premiumPlan->addEquity($lotteryEquity);
        $premiumPlan->addEquity($trafficEquity);
        $premiumPlan->addEquity($storageEquity);

        $manager->persist($premiumPlan);

        $enterprisePlan = new Plan();
        $enterprisePlan->setName('企业版');
        $enterprisePlan->setDescription('适合大型企业的企业版本');
        $enterprisePlan->setPeriodDay(365);
        $enterprisePlan->setValid(true);
        $enterprisePlan->addEquity($lotteryEquity);
        $enterprisePlan->addEquity($trafficEquity);
        $enterprisePlan->addEquity($storageEquity);
        $enterprisePlan->addEquity($bandwidthEquity);

        $manager->persist($enterprisePlan);

        $manager->flush();

        $this->addReference(self::PLAN_BASIC, $basicPlan);
        $this->addReference(self::PLAN_PREMIUM, $premiumPlan);
        $this->addReference(self::PLAN_ENTERPRISE, $enterprisePlan);
    }

    public function getDependencies(): array
    {
        return [
            EquityFixtures::class,
        ];
    }
}
