<?php

declare(strict_types=1);

namespace Tourze\SubscriptionBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use Tourze\SubscriptionBundle\Controller\Admin\EquityCrudController;
use Tourze\SubscriptionBundle\Entity\Equity;

/**
 * 权益管理控制器测试
 * @internal
 */
#[CoversClass(EquityCrudController::class)]
#[RunTestsInSeparateProcesses]
final class EquityCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    public function testGetEntityFqcn(): void
    {
        self::assertSame(Equity::class, EquityCrudController::getEntityFqcn());
    }

    /**
     * @return AbstractCrudController<Equity>
     */
    protected function getControllerService(): AbstractCrudController
    {
        return new EquityCrudController();
    }

    /** @return iterable<string, array{string}> */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield '权益名称' => ['权益名称'];
        yield '权益类型' => ['权益类型'];
        yield '权益数值' => ['权益数值'];
    }

    /** @return iterable<string, array{string}> */
    public static function provideNewPageFields(): iterable
    {
        yield 'name' => ['name'];
        yield 'type' => ['type'];
        yield 'value' => ['value'];
    }

    /** @return iterable<string, array{string}> */
    public static function provideEditPageFields(): iterable
    {
        yield 'name' => ['name'];
        yield 'type' => ['type'];
        yield 'value' => ['value'];
    }

    public function testConfigureFields(): void
    {
        $controller = new EquityCrudController();
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
        self::assertContains('name', $fieldNames);
        self::assertContains('type', $fieldNames);
        self::assertContains('value', $fieldNames);
        self::assertContains('description', $fieldNames);
        self::assertContains('plans', $fieldNames);
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
