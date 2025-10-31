<?php

declare(strict_types=1);

namespace Tourze\SubscriptionBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use Tourze\SubscriptionBundle\Controller\Admin\PlanCrudController;
use Tourze\SubscriptionBundle\Entity\Plan;

/**
 * 订阅计划管理控制器测试
 * @internal
 */
#[CoversClass(PlanCrudController::class)]
#[RunTestsInSeparateProcesses]
final class PlanCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    public function testGetEntityFqcn(): void
    {
        self::assertSame(Plan::class, PlanCrudController::getEntityFqcn());
    }

    /**
     * @return AbstractCrudController<Plan>
     */
    protected function getControllerService(): AbstractCrudController
    {
        return new PlanCrudController();
    }

    /** @return iterable<string, array{string}> */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield '有效状态' => ['有效状态'];
        yield '计划名称' => ['计划名称'];
        yield '生效天数' => ['生效天数'];
        yield '可续订次数' => ['可续订次数'];
    }

    /** @return iterable<string, array{string}> */
    public static function provideNewPageFields(): iterable
    {
        yield 'name' => ['name'];
        yield 'periodDay' => ['periodDay'];
        yield 'renewCount' => ['renewCount'];
    }

    /** @return iterable<string, array{string}> */
    public static function provideEditPageFields(): iterable
    {
        yield 'name' => ['name'];
        yield 'periodDay' => ['periodDay'];
        yield 'renewCount' => ['renewCount'];
    }

    public function testConfigureFields(): void
    {
        $controller = new PlanCrudController();
        $fields = $controller->configureFields('index');

        self::assertIsIterable($fields);

        $fieldNames = [];
        foreach ($fields as $field) {
            if (!is_string($field)) {
                $fieldNames[] = $field->getAsDto()->getProperty();
            }
        }

        // 验证包含必要字段
        self::assertContains('id', $fieldNames);
        self::assertContains('valid', $fieldNames);
        self::assertContains('name', $fieldNames);
        self::assertContains('description', $fieldNames);
        self::assertContains('periodDay', $fieldNames);
        self::assertContains('renewCount', $fieldNames);
        self::assertContains('equities', $fieldNames);
        self::assertContains('records', $fieldNames);
        self::assertContains('createTime', $fieldNames);
        self::assertContains('updateTime', $fieldNames);
    }

    /**
     * 测试表单验证错误
     */
    public function testValidationErrors(): void
    {
        $client = $this->createAuthenticatedClient();

        // 获取新建表单页面
        $crawler = $client->request('GET', $this->generateAdminUrl('new'));
        $this->assertResponseIsSuccessful();

        // 获取表单并提交空数据
        $form = $crawler->selectButton('Create')->form();
        $crawler = $client->submit($form);

        // 验证返回验证错误状态码
        $this->assertResponseStatusCodeSame(422);

        // 验证包含验证错误信息
        $this->assertStringContainsString('should not be blank', $crawler->filter('.invalid-feedback')->text());
    }
}
