<?php

declare(strict_types=1);

namespace Tourze\SubscriptionBundle\Service;

use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use Tourze\SubscriptionBundle\Entity\Equity;
use Tourze\SubscriptionBundle\Entity\Plan;
use Tourze\SubscriptionBundle\Entity\Record;
use Tourze\SubscriptionBundle\Entity\Resource;
use Tourze\SubscriptionBundle\Entity\Usage;

#[Autoconfigure(public: true)]
readonly class AdminMenu implements MenuProviderInterface
{
    public function __construct(private LinkGeneratorInterface $linkGenerator)
    {
    }

    public function __invoke(ItemInterface $item): void
    {
        if (null === $item->getChild('订阅管理')) {
            $item->addChild('订阅管理');
        }

        $subscriptionMenu = $item->getChild('订阅管理');

        if (null === $subscriptionMenu) {
            return;
        }

        // 订阅计划管理
        $subscriptionMenu
            ->addChild('订阅计划')
            ->setUri($this->linkGenerator->getCurdListPage(Plan::class))
            ->setAttribute('icon', 'fas fa-layer-group')
        ;

        // 权益管理
        $subscriptionMenu
            ->addChild('权益管理')
            ->setUri($this->linkGenerator->getCurdListPage(Equity::class))
            ->setAttribute('icon', 'fas fa-gift')
        ;

        // 订阅记录
        $subscriptionMenu
            ->addChild('订阅记录')
            ->setUri($this->linkGenerator->getCurdListPage(Record::class))
            ->setAttribute('icon', 'fas fa-history')
        ;

        // 资源情况
        $subscriptionMenu
            ->addChild('资源情况')
            ->setUri($this->linkGenerator->getCurdListPage(Resource::class))
            ->setAttribute('icon', 'fas fa-database')
        ;

        // 资源消耗
        $subscriptionMenu
            ->addChild('资源消耗')
            ->setUri($this->linkGenerator->getCurdListPage(Usage::class))
            ->setAttribute('icon', 'fas fa-chart-line')
        ;
    }
}
