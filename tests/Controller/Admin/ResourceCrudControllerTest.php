<?php

declare(strict_types=1);

namespace Tourze\SubscriptionBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use Tourze\SubscriptionBundle\Controller\Admin\ResourceCrudController;
use Tourze\SubscriptionBundle\Entity\Resource;

/**
 * 订阅资源管理控制器测试
 * @internal
 */
#[CoversClass(ResourceCrudController::class)]
#[RunTestsInSeparateProcesses]
final class ResourceCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    /** @return iterable<string, array{string}> */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield '有效状态' => ['有效状态'];
        yield '用户' => ['用户'];
        yield '订阅记录' => ['订阅记录'];
        yield '权益' => ['权益'];
        yield '开始时间' => ['开始时间'];
        yield '结束时间' => ['结束时间'];
        yield '剩余数量' => ['剩余数量'];
    }

    public function testGetEntityFqcn(): void
    {
        self::assertSame(Resource::class, ResourceCrudController::getEntityFqcn());
    }

    /**
     * @return AbstractCrudController<Resource>
     */
    protected function getControllerService(): AbstractCrudController
    {
        return new ResourceCrudController();
    }

    /** @return iterable<string, array{string}> */
    public static function provideNewPageFields(): iterable
    {
        yield 'startTime' => ['startTime'];
        yield 'endTime' => ['endTime'];
        yield 'value' => ['value'];
    }

    /** @return iterable<string, array{string}> */
    public static function provideEditPageFields(): iterable
    {
        yield 'startTime' => ['startTime'];
        yield 'endTime' => ['endTime'];
        yield 'value' => ['value'];
    }

    public function testConfigureFields(): void
    {
        $controller = new ResourceCrudController();
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
        self::assertContains('user', $fieldNames);
        self::assertContains('record', $fieldNames);
        self::assertContains('equity', $fieldNames);
        self::assertContains('startTime', $fieldNames);
        self::assertContains('endTime', $fieldNames);
        self::assertContains('value', $fieldNames);
    }

    public function testConfigureFieldsForDifferentPages(): void
    {
        $controller = new ResourceCrudController();

        // 测试detail页面字段
        $detailFields = $controller->configureFields('detail');

        self::assertIsIterable($detailFields);

        $detailFieldNames = [];
        foreach ($detailFields as $field) {
            if (!is_string($field)) {
                $detailFieldNames[] = $field->getAsDto()->getProperty();
            }
        }

        // detail页面应该包含createTime和updateTime
        self::assertContains('createTime', $detailFieldNames);
        self::assertContains('updateTime', $detailFieldNames);
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
        // 为必填字段提供无效值以触发验证错误，避免类型错误
        $form->setValues([
            'Resource[startTime]' => 'invalid-date',
            'Resource[value]' => '', // 保持此字段为空以触发验证错误
        ]);
        $crawler = $client->submit($form);

        // 验证返回验证错误状态码
        $this->assertResponseStatusCodeSame(422);

        // 验证包含验证错误信息
        $errorText = $crawler->filter('.invalid-feedback')->text();
        $this->assertTrue(
            str_contains($errorText, 'should not be blank') || str_contains($errorText, 'Please enter a valid'),
            "Expected validation error message, got: $errorText"
        );
    }
}
