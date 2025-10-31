<?php

declare(strict_types=1);

namespace Tourze\SubscriptionBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use Tourze\SubscriptionBundle\Controller\Admin\RecordCrudController;
use Tourze\SubscriptionBundle\Entity\Record;

/**
 * 订阅记录管理控制器测试
 * @internal
 */
#[CoversClass(RecordCrudController::class)]
#[RunTestsInSeparateProcesses]
final class RecordCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    public function testGetEntityFqcn(): void
    {
        self::assertSame(Record::class, RecordCrudController::getEntityFqcn());
    }

    /**
     * @return AbstractCrudController<Record>
     */
    protected function getControllerService(): AbstractCrudController
    {
        return new RecordCrudController();
    }

    /** @return iterable<string, array{string}> */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield '有效状态' => ['有效状态'];
        yield '订阅套餐' => ['订阅套餐'];
        yield '激活时间' => ['激活时间'];
        yield '过期时间' => ['过期时间'];
        yield '用户' => ['用户'];
        yield '订阅状态' => ['订阅状态'];
    }

    /** @return iterable<string, array{string}> */
    public static function provideNewPageFields(): iterable
    {
        yield 'activeTime' => ['activeTime'];
        yield 'expireTime' => ['expireTime'];
    }

    /** @return iterable<string, array{string}> */
    public static function provideEditPageFields(): iterable
    {
        yield 'activeTime' => ['activeTime'];
        yield 'expireTime' => ['expireTime'];
    }

    public function testConfigureFields(): void
    {
        $controller = new RecordCrudController();
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
        self::assertContains('plan', $fieldNames);
        self::assertContains('activeTime', $fieldNames);
        self::assertContains('expireTime', $fieldNames);
        self::assertContains('user', $fieldNames);
        self::assertContains('status', $fieldNames);
    }

    public function testConfigureFieldsForDifferentPages(): void
    {
        $controller = new RecordCrudController();

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

        // 获取表单并提交部分无效数据（不提供required字段）
        $form = $crawler->selectButton('Create')->form();
        // 只填充必填的DateTime字段以避免类型错误，但不填充其他必填字段
        $form['Record[activeTime]'] = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        $form['Record[expireTime]'] = (new \DateTimeImmutable('+30 days'))->format('Y-m-d H:i:s');
        $crawler = $client->submit($form);

        // 验证返回验证错误状态码
        $this->assertResponseStatusCodeSame(422);

        // 验证包含验证错误信息（其他必填字段未填写）
        $errorText = $crawler->filter('.invalid-feedback')->text();
        $this->assertTrue(
            str_contains($errorText, 'should not be null') || str_contains($errorText, 'should not be blank'),
            'Expected validation error message but got: ' . $errorText
        );
    }
}
