<?php

declare(strict_types=1);

namespace Tourze\SubscriptionBundle\Tests\Service;

use Knp\Menu\ItemInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;
use Tourze\SubscriptionBundle\Entity\Equity;
use Tourze\SubscriptionBundle\Entity\Plan;
use Tourze\SubscriptionBundle\Entity\Record;
use Tourze\SubscriptionBundle\Entity\Resource;
use Tourze\SubscriptionBundle\Entity\Usage;
use Tourze\SubscriptionBundle\Service\AdminMenu;

/**
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    protected function onSetUp(): void
    {
        // 基类要求实现此方法用于集成测试设置
    }

    public function testInvokeCreatesSubscriptionManagementMenu(): void
    {
        $linkGenerator = $this->createMock(LinkGeneratorInterface::class);
        $linkGenerator->method('getCurdListPage')->willReturn('/admin/entity/list');

        // 将Mock服务注册到容器
        self::getContainer()->set(LinkGeneratorInterface::class, $linkGenerator);

        $rootItem = $this->createMock(ItemInterface::class);
        $subscriptionItem = $this->createMock(ItemInterface::class);
        $menuItem = $this->createMock(ItemInterface::class);

        // 模拟主菜单没有订阅管理子菜单的情况，然后获取创建后的菜单
        // getChild('订阅管理') 会被调用两次：第一次检查是否存在，第二次获取创建后的菜单
        $rootItem->expects($this->exactly(2))
            ->method('getChild')
            ->with('订阅管理')
            ->willReturnOnConsecutiveCalls(null, $subscriptionItem)
        ;

        // 创建订阅管理菜单
        $rootItem->expects($this->once())
            ->method('addChild')
            ->with('订阅管理')
            ->willReturn($subscriptionItem)
        ;

        // 添加所有子菜单项
        $subscriptionItem->expects($this->exactly(5))
            ->method('addChild')
            ->willReturn($menuItem)
        ;

        $menuItem->expects($this->exactly(5))
            ->method('setUri')
            ->with('/admin/entity/list')
            ->willReturn($menuItem)
        ;

        $menuItem->expects($this->exactly(5))
            ->method('setAttribute')
            ->with('icon', self::anything())
            ->willReturn($menuItem)
        ;

        $adminMenu = self::getService(AdminMenu::class);
        $adminMenu($rootItem);
    }

    public function testInvokeUsesExistingSubscriptionManagementMenu(): void
    {
        $linkGenerator = $this->createMock(LinkGeneratorInterface::class);
        $linkGenerator->method('getCurdListPage')->willReturn('/admin/entity/list');

        // 将Mock服务注册到容器
        self::getContainer()->set(LinkGeneratorInterface::class, $linkGenerator);

        $rootItem = $this->createMock(ItemInterface::class);
        $subscriptionItem = $this->createMock(ItemInterface::class);
        $menuItem = $this->createMock(ItemInterface::class);

        // 模拟主菜单已有订阅管理子菜单的情况
        // getChild('订阅管理') 会被调用两次：第一次检查是否存在，第二次获取已存在的菜单
        $rootItem->expects($this->exactly(2))
            ->method('getChild')
            ->with('订阅管理')
            ->willReturn($subscriptionItem)
        ;

        // 不应该创建新的订阅管理菜单
        $rootItem->expects($this->never())->method('addChild');

        // 添加所有子菜单项
        $subscriptionItem->expects($this->exactly(5))
            ->method('addChild')
            ->willReturn($menuItem)
        ;

        $menuItem->expects($this->exactly(5))
            ->method('setUri')
            ->with('/admin/entity/list')
            ->willReturn($menuItem)
        ;

        $menuItem->expects($this->exactly(5))
            ->method('setAttribute')
            ->with('icon', self::anything())
            ->willReturn($menuItem)
        ;

        $adminMenu = self::getService(AdminMenu::class);
        $adminMenu($rootItem);
    }

    public function testCorrectEntityClassesAreUsedForLinks(): void
    {
        $linkGenerator = $this->createMock(LinkGeneratorInterface::class);
        $linkGenerator->expects($this->exactly(5))
            ->method('getCurdListPage')
            ->willReturn('/admin/entity/list')
        ;

        // 将Mock服务注册到容器
        self::getContainer()->set(LinkGeneratorInterface::class, $linkGenerator);

        $rootItem = $this->createMock(ItemInterface::class);
        $subscriptionItem = $this->createMock(ItemInterface::class);
        $menuItem = $this->createMock(ItemInterface::class);

        $rootItem->method('getChild')->willReturn($subscriptionItem);
        $subscriptionItem->method('addChild')->willReturn($menuItem);
        $menuItem->method('setUri')->willReturn($menuItem);
        $menuItem->method('setAttribute')->willReturn($menuItem);

        $adminMenu = self::getService(AdminMenu::class);
        $adminMenu($rootItem);
    }

    public function testMenuItemsHaveCorrectIcons(): void
    {
        $linkGenerator = $this->createMock(LinkGeneratorInterface::class);
        $linkGenerator->method('getCurdListPage')->willReturn('/admin/entity/list');

        // 将Mock服务注册到容器
        self::getContainer()->set(LinkGeneratorInterface::class, $linkGenerator);

        $rootItem = $this->createMock(ItemInterface::class);
        $subscriptionItem = $this->createMock(ItemInterface::class);
        $menuItem = $this->createMock(ItemInterface::class);

        $rootItem->method('getChild')->willReturn($subscriptionItem);
        $subscriptionItem->method('addChild')->willReturn($menuItem);
        $menuItem->method('setUri')->willReturn($menuItem);

        $menuItem->expects($this->exactly(5))
            ->method('setAttribute')
            ->with('icon', self::anything())
            ->willReturn($menuItem)
        ;

        $adminMenu = self::getService(AdminMenu::class);
        $adminMenu($rootItem);
    }

    public function testSpecificMenuItemsAndParametersAreCorrect(): void
    {
        $linkGenerator = $this->createMock(LinkGeneratorInterface::class);

        // 预期的调用参数
        $expectedEntityClasses = [Plan::class, Equity::class, Record::class, Resource::class, Usage::class];
        $expectedMenuLabels = ['订阅计划', '权益管理', '订阅记录', '资源情况', '资源消耗'];
        $expectedIcons = ['fas fa-layer-group', 'fas fa-gift', 'fas fa-history', 'fas fa-database', 'fas fa-chart-line'];

        // 验证getCurdListPage方法调用的参数
        $callIndex = 0;
        $linkGenerator->expects($this->exactly(5))
            ->method('getCurdListPage')
            ->willReturnCallback(function ($entityClass) use ($expectedEntityClasses, &$callIndex) {
                $this->assertEquals($expectedEntityClasses[$callIndex], $entityClass);
                ++$callIndex;

                return '/admin/entity/list';
            })
        ;

        // 将Mock服务注册到容器
        self::getContainer()->set(LinkGeneratorInterface::class, $linkGenerator);

        $rootItem = $this->createMock(ItemInterface::class);
        $subscriptionItem = $this->createMock(ItemInterface::class);
        $menuItem = $this->createMock(ItemInterface::class);

        $rootItem->method('getChild')->willReturn($subscriptionItem);

        // 验证addChild方法调用的菜单标签
        $menuCallIndex = 0;
        $subscriptionItem->expects($this->exactly(5))
            ->method('addChild')
            ->willReturnCallback(function ($label) use ($expectedMenuLabels, &$menuCallIndex, $menuItem) {
                $this->assertEquals($expectedMenuLabels[$menuCallIndex], $label);
                ++$menuCallIndex;

                return $menuItem;
            })
        ;

        $menuItem->method('setUri')->willReturn($menuItem);

        // 验证setAttribute方法调用的图标
        $iconCallIndex = 0;
        $menuItem->expects($this->exactly(5))
            ->method('setAttribute')
            ->willReturnCallback(function ($attribute, $value) use ($expectedIcons, &$iconCallIndex, $menuItem) {
                $this->assertEquals('icon', $attribute);
                $this->assertEquals($expectedIcons[$iconCallIndex], $value);
                ++$iconCallIndex;

                return $menuItem;
            })
        ;

        $adminMenu = self::getService(AdminMenu::class);
        $adminMenu($rootItem);
    }
}
