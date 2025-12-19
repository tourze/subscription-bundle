<?php

declare(strict_types=1);

namespace Tourze\SubscriptionBundle\Tests\Service;

use Knp\Menu\MenuFactory;
use Knp\Menu\MenuItem;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;
use Tourze\SubscriptionBundle\Service\AdminMenu;

/**
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    private AdminMenu $adminMenu;
    private LinkGeneratorInterface $linkGenerator;

    protected function onSetUp(): void
    {
        $this->linkGenerator = new class implements LinkGeneratorInterface {
            public function getCurdListPage(string $entityClass): string
            {
                return '/admin/entity/list';
            }

            public function extractEntityFqcn(string $url): ?string
            {
                return null;
            }

            public function setDashboard(string $dashboardControllerFqcn): void
            {
                // Mock implementation - do nothing
            }
        };

        // 将Mock服务注册到容器
        self::getContainer()->set(LinkGeneratorInterface::class, $this->linkGenerator);

        // 从容器中获取服务
        $this->adminMenu = self::getService(AdminMenu::class);
    }

    public function testMenuBuilding(): void
    {
        $menuFactory = new MenuFactory();
        $rootMenu = new MenuItem('root', $menuFactory);

        ($this->adminMenu)($rootMenu);

        $subscriptionMenu = $rootMenu->getChild('订阅管理');
        $this->assertNotNull($subscriptionMenu, '订阅管理菜单应该被创建');

        // 验证所有子菜单项都被创建
        $this->assertNotNull($subscriptionMenu->getChild('订阅计划'));
        $this->assertNotNull($subscriptionMenu->getChild('权益管理'));
        $this->assertNotNull($subscriptionMenu->getChild('订阅记录'));
        $this->assertNotNull($subscriptionMenu->getChild('资源情况'));
        $this->assertNotNull($subscriptionMenu->getChild('资源消耗'));
    }

    public function testMenuBuildingWithExistingMenu(): void
    {
        $menuFactory = new MenuFactory();
        $rootMenu = new MenuItem('root', $menuFactory);
        $existingSubscriptionMenu = new MenuItem('订阅管理', $menuFactory);
        $rootMenu->addChild($existingSubscriptionMenu);

        ($this->adminMenu)($rootMenu);

        $subscriptionMenu = $rootMenu->getChild('订阅管理');
        $this->assertSame($existingSubscriptionMenu, $subscriptionMenu, '应该使用已存在的订阅管理菜单');

        // 验证所有子菜单项都被创建
        $this->assertNotNull($subscriptionMenu->getChild('订阅计划'));
        $this->assertNotNull($subscriptionMenu->getChild('权益管理'));
        $this->assertNotNull($subscriptionMenu->getChild('订阅记录'));
        $this->assertNotNull($subscriptionMenu->getChild('资源情况'));
        $this->assertNotNull($subscriptionMenu->getChild('资源消耗'));
    }

    public function testMenuItemsHaveCorrectUris(): void
    {
        $menuFactory = new MenuFactory();
        $rootMenu = new MenuItem('root', $menuFactory);

        ($this->adminMenu)($rootMenu);

        $subscriptionMenu = $rootMenu->getChild('订阅管理');
        $this->assertNotNull($subscriptionMenu);

        // 验证所有子菜单项都有正确的URI
        $expectedMenuItems = ['订阅计划', '权益管理', '订阅记录', '资源情况', '资源消耗'];
        foreach ($expectedMenuItems as $itemName) {
            $menuItem = $subscriptionMenu->getChild($itemName);
            $this->assertNotNull($menuItem, "菜单项 '{$itemName}' 应该存在");
            $this->assertEquals('/admin/entity/list', $menuItem->getUri(), "菜单项 '{$itemName}' 应该有正确的URI");
        }
    }

    public function testMenuItemsHaveCorrectIcons(): void
    {
        $menuFactory = new MenuFactory();
        $rootMenu = new MenuItem('root', $menuFactory);

        ($this->adminMenu)($rootMenu);

        $subscriptionMenu = $rootMenu->getChild('订阅管理');
        $this->assertNotNull($subscriptionMenu);

        $expectedIcons = [
            '订阅计划' => 'fas fa-layer-group',
            '权益管理' => 'fas fa-gift',
            '订阅记录' => 'fas fa-history',
            '资源情况' => 'fas fa-database',
            '资源消耗' => 'fas fa-chart-line',
        ];

        foreach ($expectedIcons as $menuLabel => $expectedIcon) {
            $menuItem = $subscriptionMenu->getChild($menuLabel);
            $this->assertNotNull($menuItem, "菜单项 '{$menuLabel}' 应该存在");
            $this->assertEquals($expectedIcon, $menuItem->getAttribute('icon'), "菜单项 '{$menuLabel}' 应该有正确的图标");
        }
    }

    
    public function testConstructor(): void
    {
        // 验证 AdminMenu 实例创建成功，依赖注入正常
        $this->assertInstanceOf(AdminMenu::class, $this->adminMenu);
    }
}
