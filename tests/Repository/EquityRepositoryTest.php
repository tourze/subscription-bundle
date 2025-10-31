<?php

namespace Tourze\SubscriptionBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\QueryException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use Tourze\SubscriptionBundle\Entity\Equity;
use Tourze\SubscriptionBundle\Entity\Plan;
use Tourze\SubscriptionBundle\Repository\EquityRepository;
use Tourze\SubscriptionBundle\Repository\PlanRepository;

/**
 * @internal
 */
#[CoversClass(EquityRepository::class)]
#[RunTestsInSeparateProcesses]
final class EquityRepositoryTest extends AbstractRepositoryTestCase
{
    private EquityRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(EquityRepository::class);
    }

    public function testRepositoryManagesCorrectEntity(): void
    {
        $entityClass = $this->repository->getClassName();
        $this->assertSame(Equity::class, $entityClass);
    }

    /**
     * 测试 save 方法
     */
    public function testSaveMethodPersistsEntity(): void
    {
        $user = $this->createNormalUser('test@example.com');

        $equity = new Equity();
        $equity->setName('抽奖次数');
        $equity->setType('lottery');
        $equity->setValue('10');
        $equity->setDescription('每月免费抽奖10次');
        $equity->setCreatedBy($user->getUserIdentifier());
        $equity->setUpdatedBy($user->getUserIdentifier());

        $this->repository->save($equity);

        $this->assertNotNull($equity->getId());
        $this->assertGreaterThan(0, $equity->getId());

        $foundEquity = $this->repository->find($equity->getId());
        $this->assertNotNull($foundEquity);
        $this->assertEquals('抽奖次数', $foundEquity->getName());
        $this->assertEquals('lottery', $foundEquity->getType());
        $this->assertEquals('10', $foundEquity->getValue());
    }

    /**
     * 测试 save 方法不自动提交
     */
    public function testSaveMethodWithoutFlush(): void
    {
        $user = $this->createNormalUser('test@example.com');

        $equity = new Equity();
        $equity->setName('流量包');
        $equity->setType('traffic');
        $equity->setValue('100');
        $equity->setCreatedBy($user->getUserIdentifier());
        $equity->setUpdatedBy($user->getUserIdentifier());

        $this->repository->save($equity, false);

        // 实体应该被持久化但还没有ID
        $this->assertEquals(0, $equity->getId());
    }

    /**
     * 测试 remove 方法
     */
    public function testRemoveMethodDeletesEntity(): void
    {
        $user = $this->createNormalUser('test@example.com');

        $equity = new Equity();
        $equity->setName('积分奖励');
        $equity->setType('points');
        $equity->setValue('500');
        $equity->setCreatedBy($user->getUserIdentifier());
        $equity->setUpdatedBy($user->getUserIdentifier());

        $this->repository->save($equity);
        $equityId = $equity->getId();

        $this->repository->remove($equity);

        $deletedEquity = $this->repository->find($equityId);
        $this->assertNull($deletedEquity);
    }

    /**
     * 测试 remove 方法不自动提交
     */
    public function testRemoveMethodWithoutFlush(): void
    {
        $user = $this->createNormalUser('test@example.com');

        $equity = new Equity();
        $equity->setName('优惠券');
        $equity->setType('coupon');
        $equity->setValue('20');
        $equity->setCreatedBy($user->getUserIdentifier());
        $equity->setUpdatedBy($user->getUserIdentifier());

        $this->repository->save($equity);
        $equityId = $equity->getId();

        $this->repository->remove($equity, false);

        // 实体还应该存在，因为没有提交
        $foundEquity = $this->repository->find($equityId);
        $this->assertNotNull($foundEquity);
    }

    /**
     * 测试 find 方法使用有效 ID
     */
    public function testFindWithValidIdShouldReturnEntity(): void
    {
        $user = $this->createNormalUser('test@example.com');

        $equity = new Equity();
        $equity->setName('会员积分');
        $equity->setType('points');
        $equity->setValue('1000');
        $equity->setCreatedBy($user->getUserIdentifier());
        $equity->setUpdatedBy($user->getUserIdentifier());

        $this->repository->save($equity);
        $equityId = $equity->getId();

        $foundEquity = $this->repository->find($equityId);
        $this->assertNotNull($foundEquity);
        $this->assertInstanceOf(Equity::class, $foundEquity);
        $this->assertEquals($equityId, $foundEquity->getId());
        $this->assertEquals('会员积分', $foundEquity->getName());
    }

    /**
     * 测试 find 方法使用负数作为 ID
     */

    /**
     * 测试 findAll 方法 - 当记录存在时返回实体数组
     */

    /**
     * 测试 findAll 方法 - 当没有记录时返回空数组
     */

    /**
     * 测试 findOneBy 方法 - 使用匹配条件应返回实体
     */

    /**
     * 测试 findBy 方法 - 使用匹配条件应返回实体数组
     */

    /**
     * 测试 findBy 方法 - 遵守排序条款
     */

    /**
     * 测试 findBy 方法 - 遵守限制和偏移参数
     */

    /**
     * 测试可空字段查询 - findOneBy description as null
     */

    /**
     * 测试可空字段查询所有匹配的实体 - findBy description as null
     */

    /**
     * 测试可空字段计数 - count by description as null
     */

    /**
     * 测试关联查询 - 通过 Plan 查找 Equity
     */
    public function testFindEquitiesByPlan(): void
    {
        $user = $this->createNormalUser('test@example.com');

        // 创建订阅计划
        $plan = new Plan();
        $plan->setName('高级会员');
        $plan->setDescription('高级会员计划');
        $plan->setPeriodDay(30);
        $plan->setRenewCount(12);
        $plan->setValid(true);
        $plan->setCreatedBy($user->getUserIdentifier());
        $plan->setUpdatedBy($user->getUserIdentifier());

        $planRepository = self::getService(PlanRepository::class);
        $planRepository->save($plan);

        // 创建权益
        $equity1 = new Equity();
        $equity1->setName('VIP积分');
        $equity1->setType('points');
        $equity1->setValue('1000');
        $equity1->setCreatedBy($user->getUserIdentifier());
        $equity1->setUpdatedBy($user->getUserIdentifier());

        $equity2 = new Equity();
        $equity2->setName('专属流量');
        $equity2->setType('traffic');
        $equity2->setValue('5000');
        $equity2->setCreatedBy($user->getUserIdentifier());
        $equity2->setUpdatedBy($user->getUserIdentifier());

        $this->repository->save($equity1);
        $this->repository->save($equity2);

        // 建立关联 - 需要在双方都设置关联
        $plan->addEquity($equity1);
        $plan->addEquity($equity2);
        $planRepository->save($plan);

        // 验证关联关系
        $this->assertTrue($equity1->getPlans()->contains($plan));
        $this->assertTrue($equity2->getPlans()->contains($plan));
        $this->assertTrue($plan->getEquities()->contains($equity1));
        $this->assertTrue($plan->getEquities()->contains($equity2));
    }

    /**
     * 测试使用不存在的字段进行查询 - findBy 方法
     */

    /**
     * 测试使用不存在的字段进行计数
     */

    /**
     * 测试数据库连接失败时的行为
     */
    public function testDatabaseExceptionHandling(): void
    {
        $this->expectException(QueryException::class);
        $this->expectExceptionMessage('Expected known function');

        $this->repository->createQueryBuilder('e')
            ->select('INVALID_FUNCTION()')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * 测试 findOneBy 排序逻辑
     */
    public function testFindOneByWithOrderByClause(): void
    {
        $user = $this->createNormalUser('test@example.com');

        // 创建多个相同类型的权益，确保有不同的值以便排序
        $equity1 = new Equity();
        $equity1->setName('权益1');
        $equity1->setType('order_test');
        $equity1->setValue('100');
        $equity1->setCreatedBy($user->getUserIdentifier());
        $equity1->setUpdatedBy($user->getUserIdentifier());
        $this->repository->save($equity1);

        sleep(1); // 确保时间差异

        $equity2 = new Equity();
        $equity2->setName('权益2');
        $equity2->setType('order_test');
        $equity2->setValue('200');
        $equity2->setCreatedBy($user->getUserIdentifier());
        $equity2->setUpdatedBy($user->getUserIdentifier());
        $this->repository->save($equity2);

        sleep(1); // 确保时间差异

        $equity3 = new Equity();
        $equity3->setName('权益3');
        $equity3->setType('order_test');
        $equity3->setValue('300');
        $equity3->setCreatedBy($user->getUserIdentifier());
        $equity3->setUpdatedBy($user->getUserIdentifier());
        $this->repository->save($equity3);

        // 测试按值升序排序，应该返回最小值的
        $lowestValueEquity = $this->repository->findOneBy(['type' => 'order_test'], ['value' => 'ASC']);
        $this->assertNotNull($lowestValueEquity);
        $this->assertEquals('100', $lowestValueEquity->getValue());

        // 测试按值降序排序，应该返回最大值的
        $highestValueEquity = $this->repository->findOneBy(['type' => 'order_test'], ['value' => 'DESC']);
        $this->assertNotNull($highestValueEquity);
        $this->assertEquals('300', $highestValueEquity->getValue());

        // 测试按创建时间升序排序
        $oldestEquity = $this->repository->findOneBy(['type' => 'order_test'], ['createTime' => 'ASC']);
        $this->assertNotNull($oldestEquity);
        $this->assertEquals('权益1', $oldestEquity->getName());

        // 测试按创建时间降序排序
        $newestEquity = $this->repository->findOneBy(['type' => 'order_test'], ['createTime' => 'DESC']);
        $this->assertNotNull($newestEquity);
        $this->assertEquals('权益3', $newestEquity->getName());
    }

    /**
     * 测试 findOneBy 排序逻辑 - 按类型查询并按值排序
     */

    /**
     * 测试 find 方法 - 使用存在的 ID
     */

    /**
     * 测试数据库连接失败时的行为 - findAll 方法
     */

    /**
     * 测试数据库连接失败时的行为 - count 方法
     */
    protected function createNewEntity(): object
    {
        $user = $this->createNormalUser('test@example.com');

        $entity = new Equity();
        $entity->setName('Test Equity ' . uniqid());
        $entity->setType('test_type');
        $entity->setValue('100');
        $entity->setDescription('Test equity description');
        $entity->setCreatedBy($user->getUserIdentifier());
        $entity->setUpdatedBy($user->getUserIdentifier());

        return $entity;
    }

    /**
     * @return EquityRepository
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}
