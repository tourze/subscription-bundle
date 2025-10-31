<?php

namespace Tourze\SubscriptionBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use Tourze\SubscriptionBundle\Entity\Plan;
use Tourze\SubscriptionBundle\Entity\Record;
use Tourze\SubscriptionBundle\Enum\SubscribeStatus;
use Tourze\SubscriptionBundle\Repository\PlanRepository;
use Tourze\SubscriptionBundle\Repository\RecordRepository;

/**
 * @internal
 */
#[CoversClass(RecordRepository::class)]
#[RunTestsInSeparateProcesses]
final class RecordRepositoryTest extends AbstractRepositoryTestCase
{
    private RecordRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(RecordRepository::class);

        // 如果当前测试是数据库连接测试，跳过数据加载和清理操作
        if ($this->isTestingDatabaseConnection()) {
            return;
        }

        // 手动加载必要的 DataFixtures 来满足基类的 testCountWithDataFixtureShouldReturnGreaterThanZero 测试
        try {
            if (0 === $this->repository->count()) {
                $this->loadRecordFixtures();
            }
        } catch (\Exception $e) {
            // 如果数据加载失败（比如在数据库连接测试中），忽略错误
        }

        // 清理实体管理器状态，避免影响数据库连接测试
        try {
            self::getEntityManager()->clear();
        } catch (\Exception $e) {
            // 忽略清理错误
        }
    }

    private function isTestingDatabaseConnection(): bool
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        foreach ($backtrace as $trace) {
            if (str_contains($trace['function'], 'testFindWhenDatabaseIsUnavailable')) {
                return true;
            }
            if (str_contains($trace['function'], 'testFindByWhenDatabaseIsUnavailable')) {
                return true;
            }
            if (str_contains($trace['function'], 'testCountWhenDatabaseIsUnavailable')) {
                return true;
            }
            if (str_contains($trace['function'], 'testFindAllWhenDatabaseIsUnavailable')) {
                return true;
            }
        }

        return false;
    }

    private function loadRecordFixtures(): void
    {
        $manager = self::getEntityManager();

        // 首先确保有 Plan
        $planRepository = self::getService(PlanRepository::class);
        if (0 === $planRepository->count()) {
            $plan = new Plan();
            $plan->setName('Test Plan');
            $plan->setDescription('Test Plan Description');
            $plan->setPeriodDay(30);
            $plan->setRenewCount(0);
            $plan->setValid(true);
            $manager->persist($plan);
            $manager->flush();
        } else {
            $plan = $planRepository->findAll()[0];
        }

        // 创建多个Record来避免数据库连接测试的问题
        // 确保ID=1被其他记录占用，这样testFindWhenDatabaseIsUnavailable测试不会受到缓存影响
        for ($i = 0; $i < 5; ++$i) {
            $user = $this->createNormalUser('test_user_' . $i . '@example.com');
            $record = new Record();
            $record->setUser($user);
            $record->setPlan($plan);
            $record->setStatus(SubscribeStatus::ACTIVE);
            $record->setValid(true);
            $record->setActiveTime(new \DateTimeImmutable());
            $record->setExpireTime(new \DateTimeImmutable('+30 days'));

            $manager->persist($record);
        }

        $manager->flush();
    }

    public function testRepositoryManagesCorrectEntity(): void
    {
        $entityClass = $this->repository->getClassName();
        $this->assertSame(Record::class, $entityClass);
    }

    public function testFindOneByWithOrderByShouldReturnFirstMatchingEntity(): void
    {
        $user = $this->createNormalUser();
        $plan = $this->createPlan('Test Plan');

        $record1 = $this->createRecord($user, $plan, SubscribeStatus::ACTIVE);
        $record1->setActiveTime(new \DateTimeImmutable('2023-01-02'));
        $this->repository->save($record1);

        $record2 = $this->createRecord($user, $plan, SubscribeStatus::ACTIVE);
        $record2->setActiveTime(new \DateTimeImmutable('2023-01-01'));
        $this->repository->save($record2);

        $foundRecord = $this->repository->findOneBy(['status' => SubscribeStatus::ACTIVE], ['activeTime' => 'ASC']);

        $this->assertNotNull($foundRecord);
        $this->assertSame($record2->getId(), $foundRecord->getId());
    }

    public function testSave(): void
    {
        $user = $this->createNormalUser();
        $plan = $this->createPlan('Test Plan');
        $record = new Record();
        $record->setUser($user);
        $record->setPlan($plan);
        $record->setActiveTime(new \DateTimeImmutable());
        $record->setExpireTime(new \DateTimeImmutable('+30 days'));
        $record->setStatus(SubscribeStatus::ACTIVE);
        $record->setValid(true);

        $this->repository->save($record);

        $this->assertGreaterThan(0, $record->getId());
        $savedRecord = $this->repository->find($record->getId());
        $this->assertNotNull($savedRecord);
        $this->assertSame($record->getId(), $savedRecord->getId());
    }

    public function testSaveWithoutFlush(): void
    {
        $user = $this->createNormalUser();
        $plan = $this->createPlan('Test Plan');
        $record = new Record();
        $record->setUser($user);
        $record->setPlan($plan);
        $record->setActiveTime(new \DateTimeImmutable());
        $record->setExpireTime(new \DateTimeImmutable('+30 days'));
        $record->setStatus(SubscribeStatus::ACTIVE);
        $record->setValid(true);

        $this->repository->save($record, false);
        $entityManager = self::getService(EntityManagerInterface::class);
        $entityManager->flush();

        $this->assertGreaterThan(0, $record->getId());
    }

    public function testRemove(): void
    {
        $user = $this->createNormalUser();
        $plan = $this->createPlan('Test Plan');
        $record = $this->createRecord($user, $plan);
        $recordId = $record->getId();

        $this->repository->remove($record);

        $deletedRecord = $this->repository->find($recordId);
        $this->assertNull($deletedRecord);
    }

    public function testFindByAssociatedPlan(): void
    {
        $user = $this->createNormalUser();
        $plan1 = $this->createPlan('Plan 1');
        $plan2 = $this->createPlan('Plan 2');

        $record1 = $this->createRecord($user, $plan1);
        $record2 = $this->createRecord($user, $plan2);

        $records = $this->repository->findBy(['plan' => $plan1]);

        $this->assertCount(1, $records);
        $this->assertInstanceOf(Record::class, $records[0]);
        $recordPlan = $records[0]->getPlan();
        $this->assertNotNull($recordPlan);
        $this->assertSame($plan1->getId(), $recordPlan->getId());
    }

    public function testFindByAssociatedUser(): void
    {
        $user = $this->createNormalUser();
        $plan = $this->createPlan('Test Plan');

        $record1 = $this->createRecord($user, $plan);
        $record2 = $this->createRecord($user, $plan);

        $records = $this->repository->findBy(['user' => $user]);

        $this->assertCount(2, $records);
        $this->assertSame($user, $records[0]->getUser());
        $this->assertSame($user, $records[1]->getUser());
    }

    public function testCountByAssociatedPlan(): void
    {
        $user = $this->createNormalUser();
        $plan1 = $this->createPlan('Plan 1');
        $plan2 = $this->createPlan('Plan 2');

        $this->createRecord($user, $plan1);
        $this->createRecord($user, $plan1);
        $this->createRecord($user, $plan2);

        $count = $this->repository->count(['plan' => $plan1]);

        $this->assertSame(2, $count);
    }

    public function testFindByNullValidField(): void
    {
        $user = $this->createNormalUser();
        $plan = $this->createPlan('Test Plan');
        $record = $this->createRecord($user, $plan);
        $record->setValid(null);
        $this->repository->save($record);

        $records = $this->repository->findBy(['valid' => null]);

        $this->assertCount(1, $records);
        $this->assertInstanceOf(Record::class, $records[0]);
        $this->assertNull($records[0]->isValid());
    }

    public function testFindByNullStatusField(): void
    {
        $user = $this->createNormalUser();
        $plan = $this->createPlan('Test Plan');
        $record = $this->createRecord($user, $plan);
        $record->setStatus(null);
        $this->repository->save($record);

        $records = $this->repository->findBy(['status' => null]);

        $this->assertCount(1, $records);
        $this->assertInstanceOf(Record::class, $records[0]);
        $this->assertNull($records[0]->getStatus());
    }

    public function testCountByNullValidField(): void
    {
        $user = $this->createNormalUser();
        $plan = $this->createPlan('Test Plan');
        $record = $this->createRecord($user, $plan);
        $record->setValid(null);
        $this->repository->save($record);

        $count = $this->repository->count(['valid' => null]);

        $this->assertSame(1, $count);
    }

    public function testCountByNullStatusField(): void
    {
        $user = $this->createNormalUser();
        $plan = $this->createPlan('Test Plan');
        $record = $this->createRecord($user, $plan);
        $record->setStatus(null);
        $this->repository->save($record);

        $count = $this->repository->count(['status' => null]);

        $this->assertSame(1, $count);
    }

    public function testFindByMultipleNullFields(): void
    {
        $user = $this->createNormalUser();
        $plan = $this->createPlan('Test Plan');

        $record1 = $this->createRecord($user, $plan);
        $record1->setValid(null);
        $record1->setStatus(null);
        $this->repository->save($record1);

        $record2 = $this->createRecord($user, $plan);
        $record2->setValid(true);
        $record2->setStatus(null);
        $this->repository->save($record2);

        $records = $this->repository->findBy(['valid' => null, 'status' => null]);

        $this->assertCount(1, $records);
        $this->assertNull($records[0]->isValid());
        $this->assertNull($records[0]->getStatus());
    }

    public function testCountByMultipleNullFields(): void
    {
        $user = $this->createNormalUser();
        $plan = $this->createPlan('Test Plan');

        $record1 = $this->createRecord($user, $plan);
        $record1->setValid(null);
        $record1->setStatus(null);
        $this->repository->save($record1);

        $record2 = $this->createRecord($user, $plan);
        $record2->setValid(null);
        $record2->setStatus(SubscribeStatus::ACTIVE);
        $this->repository->save($record2);

        $count = $this->repository->count(['valid' => null, 'status' => null]);

        $this->assertSame(1, $count);
    }

    public function testFindOneByMultipleAssociationsWithOrderBy(): void
    {
        $user = $this->createNormalUser();
        $plan1 = $this->createPlan('Plan 1');
        $plan2 = $this->createPlan('Plan 2');

        $record1 = $this->createRecord($user, $plan1);
        $record1->setActiveTime(new \DateTimeImmutable('2024-01-02'));
        $this->repository->save($record1);

        $record2 = $this->createRecord($user, $plan2);
        $record2->setActiveTime(new \DateTimeImmutable('2024-01-01'));
        $this->repository->save($record2);

        $foundRecord = $this->repository->findOneBy(
            ['user' => $user],
            ['activeTime' => 'ASC', 'plan' => 'ASC']
        );

        $this->assertNotNull($foundRecord);
        $this->assertSame($record2->getId(), $foundRecord->getId());
    }

    public function testCountByAssociationPlanWithNullFields(): void
    {
        $user = $this->createNormalUser();
        $plan = $this->createPlan('Test Plan');

        $record1 = $this->createRecord($user, $plan);
        $record1->setValid(null);
        $this->repository->save($record1);

        $record2 = $this->createRecord($user, $plan);
        $record2->setValid(true);
        $this->repository->save($record2);

        $count = $this->repository->count(['plan' => $plan, 'valid' => null]);

        $this->assertSame(1, $count);
    }

    public function testCountByAssociationUserWithNullFields(): void
    {
        $user = $this->createNormalUser();
        $plan = $this->createPlan('Test Plan');

        $record1 = $this->createRecord($user, $plan);
        $record1->setStatus(null);
        $this->repository->save($record1);

        $record2 = $this->createRecord($user, $plan);
        $record2->setStatus(SubscribeStatus::ACTIVE);
        $this->repository->save($record2);

        $count = $this->repository->count(['user' => $user, 'status' => null]);

        $this->assertSame(1, $count);
    }

    public function testFindOneByAssociatedPlan(): void
    {
        $user = $this->createNormalUser();
        $plan1 = $this->createPlan('Plan 1');
        $plan2 = $this->createPlan('Plan 2');

        $record1 = $this->createRecord($user, $plan1);
        $record2 = $this->createRecord($user, $plan2);

        $foundRecord = $this->repository->findOneBy(['plan' => $plan1]);

        $this->assertNotNull($foundRecord);
        $this->assertInstanceOf(Record::class, $foundRecord);
        $foundRecordPlan = $foundRecord->getPlan();
        $this->assertNotNull($foundRecordPlan);
        $this->assertSame($plan1->getId(), $foundRecordPlan->getId());
    }

    public function testFindOneByAssociatedUser(): void
    {
        $user = $this->createNormalUser();
        $plan = $this->createPlan('Test Plan');

        $record1 = $this->createRecord($user, $plan);

        $foundRecord = $this->repository->findOneBy(['user' => $user]);

        $this->assertNotNull($foundRecord);
        $this->assertSame($user, $foundRecord->getUser());
    }

    public function testCountByAssociatedUser(): void
    {
        $user = $this->createNormalUser();
        $plan = $this->createPlan('Test Plan');

        $this->createRecord($user, $plan);
        $this->createRecord($user, $plan);
        $this->createRecord($user, $plan);

        $count = $this->repository->count(['user' => $user]);

        $this->assertSame(3, $count);
    }

    public function testFindOneByWithNullValidField(): void
    {
        $user = $this->createNormalUser();
        $plan = $this->createPlan('Test Plan');
        $record = $this->createRecord($user, $plan);
        $record->setValid(null);
        $this->repository->save($record);

        $foundRecord = $this->repository->findOneBy(['valid' => null]);

        $this->assertNotNull($foundRecord);
        $this->assertNull($foundRecord->isValid());
    }

    public function testFindOneByWithNullStatusField(): void
    {
        $user = $this->createNormalUser();
        $plan = $this->createPlan('Test Plan');
        $record = $this->createRecord($user, $plan);
        $record->setStatus(null);
        $this->repository->save($record);

        $foundRecord = $this->repository->findOneBy(['status' => null]);

        $this->assertNotNull($foundRecord);
        $this->assertNull($foundRecord->getStatus());
    }

    public function testFindOneByWithOrderByExpireTime(): void
    {
        $user = $this->createNormalUser();
        $plan = $this->createPlan('Test Plan');

        $record1 = $this->createRecord($user, $plan);
        $record1->setExpireTime(new \DateTimeImmutable('2024-01-02'));
        $this->repository->save($record1);

        $record2 = $this->createRecord($user, $plan);
        $record2->setExpireTime(new \DateTimeImmutable('2024-01-01'));
        $this->repository->save($record2);

        $foundRecord = $this->repository->findOneBy(['user' => $user], ['expireTime' => 'ASC']);

        $this->assertNotNull($foundRecord);
        $this->assertSame($record2->getId(), $foundRecord->getId());
    }

    public function testFindOneByWithOrderByStatus(): void
    {
        $user = $this->createNormalUser();
        $plan = $this->createPlan('Test Plan');

        $record1 = $this->createRecord($user, $plan, SubscribeStatus::EXPIRED);
        $record2 = $this->createRecord($user, $plan, SubscribeStatus::ACTIVE);

        $foundRecord = $this->repository->findOneBy(['user' => $user], ['status' => 'ASC']);

        $this->assertNotNull($foundRecord);
        $this->assertSame(SubscribeStatus::ACTIVE, $foundRecord->getStatus());
    }

    public function testFindOneByWithOrderByValidField(): void
    {
        $user = $this->createNormalUser();
        $plan = $this->createPlan('Test Plan');

        $record1 = $this->createRecord($user, $plan);
        $record1->setValid(true);
        $this->repository->save($record1);

        $record2 = $this->createRecord($user, $plan);
        $record2->setValid(false);
        $this->repository->save($record2);

        $foundRecord = $this->repository->findOneBy(['user' => $user], ['valid' => 'ASC']);

        $this->assertNotNull($foundRecord);
        $this->assertFalse($foundRecord->isValid());
    }

    public function testFindOneByWithOrderByPlanId(): void
    {
        // 创建一个独立的用户，确保测试隔离
        $user = $this->createNormalUser('unique_test_user_' . uniqid());
        $plan1 = $this->createPlan('Plan A');
        $plan2 = $this->createPlan('Plan B');

        $record1 = $this->createRecord($user, $plan2);
        $record2 = $this->createRecord($user, $plan1);

        $foundRecord = $this->repository->findOneBy(['user' => $user], ['plan' => 'ASC']);

        $this->assertNotNull($foundRecord);

        // 现在测试是隔离的，应该返回较小 ID 的 Plan
        $expectedPlanId = min($plan1->getId(), $plan2->getId());
        $this->assertInstanceOf(Record::class, $foundRecord);
        $foundRecordPlan = $foundRecord->getPlan();
        $this->assertNotNull($foundRecordPlan);
        $this->assertSame($expectedPlanId, $foundRecordPlan->getId());
    }

    public function testFindOneByAssociationPlanShouldReturnMatchingEntity(): void
    {
        $user = $this->createNormalUser();
        $plan = $this->createPlan('Test Plan');
        $record = $this->createRecord($user, $plan);

        $foundRecord = $this->repository->findOneBy(['plan' => $plan]);

        $this->assertNotNull($foundRecord);
        $this->assertInstanceOf(Record::class, $foundRecord);
        $foundRecordPlan = $foundRecord->getPlan();
        $this->assertNotNull($foundRecordPlan);
        $this->assertSame($plan->getId(), $foundRecordPlan->getId());
    }

    public function testCountByAssociationPlanShouldReturnCorrectNumber(): void
    {
        $user = $this->createNormalUser();
        $plan1 = $this->createPlan('Plan 1');
        $plan2 = $this->createPlan('Plan 2');

        $this->createRecord($user, $plan1);
        $this->createRecord($user, $plan1);
        $this->createRecord($user, $plan1);
        $this->createRecord($user, $plan2);

        $count = $this->repository->count(['plan' => $plan1]);

        $this->assertSame(3, $count);
    }

    private function createRecord(UserInterface $user, Plan $plan, ?SubscribeStatus $status = null): Record
    {
        $record = new Record();
        $record->setUser($user);
        $record->setPlan($plan);
        $record->setActiveTime(new \DateTimeImmutable());
        $record->setExpireTime(new \DateTimeImmutable('+30 days'));
        $record->setStatus($status ?? SubscribeStatus::ACTIVE);
        $record->setValid(true);

        $this->repository->save($record);

        return $record;
    }

    private function createPlan(string $name): Plan
    {
        $plan = new Plan();
        $plan->setName($name);
        $plan->setDescription('Test plan description');
        $plan->setPeriodDay(30);
        $plan->setRenewCount(0);
        $plan->setValid(true);

        $planRepository = self::getService('Tourze\SubscriptionBundle\Repository\PlanRepository');
        $planRepository->save($plan);

        return $plan;
    }

    protected function createNewEntity(): object
    {
        $user = $this->createNormalUser('test@example.com');
        $plan = $this->createPlan('Test Plan');

        $entity = new Record();
        $entity->setUser($user);
        $entity->setPlan($plan);
        $entity->setActiveTime(new \DateTimeImmutable());
        $entity->setExpireTime(new \DateTimeImmutable('+30 days'));
        $entity->setStatus(SubscribeStatus::ACTIVE);
        $entity->setValid(true);

        return $entity;
    }

    /**
     * @return RecordRepository
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}
