<?php

namespace Tourze\SubscriptionBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use Tourze\SubscriptionBundle\Entity\Equity;
use Tourze\SubscriptionBundle\Entity\Plan;
use Tourze\SubscriptionBundle\Entity\Record;
use Tourze\SubscriptionBundle\Enum\SubscribeStatus;
use Tourze\SubscriptionBundle\Repository\PlanRepository;

/**
 * @internal
 */
#[CoversClass(PlanRepository::class)]
#[RunTestsInSeparateProcesses]
final class PlanRepositoryTest extends AbstractRepositoryTestCase
{
    private PlanRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(PlanRepository::class);
    }

    public function testRepositoryManagesCorrectEntity(): void
    {
        $entityClass = $this->repository->getClassName();
        $this->assertSame(Plan::class, $entityClass);
    }

    public function testFindReturnsNullForNonExistentEntity(): void
    {
        $result = $this->repository->find(999999);
        $this->assertNull($result);
    }

    public function testFindReturnsEntityWhenExists(): void
    {
        $plan = $this->createTestPlan();
        $this->repository->save($plan);

        $found = $this->repository->find($plan->getId());
        $this->assertInstanceOf(Plan::class, $found);
        $this->assertSame($plan->getId(), $found->getId());
        $this->assertSame('Test Plan', $found->getName());
    }

    public function testFindAllReturnsEmptyArrayWhenNoEntities(): void
    {
        $this->clearDatabase();
        $result = $this->repository->findAll();
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testFindAllReturnsAllEntities(): void
    {
        $this->clearDatabase();

        $plan1 = $this->createTestPlan('Plan 1', 30);
        $plan2 = $this->createTestPlan('Plan 2', 60);
        $this->repository->save($plan1);
        $this->repository->save($plan2);

        $result = $this->repository->findAll();
        $this->assertIsArray($result);
        $this->assertCount(2, $result);

        $names = array_map(fn (Plan $p) => $p->getName(), $result);
        $this->assertContains('Plan 1', $names);
        $this->assertContains('Plan 2', $names);
    }

    public function testFindByReturnsEmptyArrayWhenNoCriteria(): void
    {
        $this->clearDatabase();
        $result = $this->repository->findBy(['name' => 'Non-existent']);
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testFindByReturnMatchingEntities(): void
    {
        $this->clearDatabase();

        $plan1 = $this->createTestPlan('Monthly Plan', 30);
        $plan2 = $this->createTestPlan('Yearly Plan', 365);
        $plan3 = $this->createTestPlan('Weekly Plan', 7);

        $this->repository->save($plan1);
        $this->repository->save($plan2);
        $this->repository->save($plan3);

        // Test finding by periodDay
        $monthlyPlans = $this->repository->findBy(['periodDay' => 30]);
        $this->assertCount(1, $monthlyPlans);
        $this->assertSame('Monthly Plan', $monthlyPlans[0]->getName());

        // Test finding with limit
        $limitedResult = $this->repository->findBy([], ['name' => 'ASC'], 2);
        $this->assertCount(2, $limitedResult);

        // Test finding with offset
        $offsetResult = $this->repository->findBy([], ['name' => 'ASC'], 2, 1);
        $this->assertCount(2, $offsetResult);
    }

    public function testFindByWithValidField(): void
    {
        $this->clearDatabase();

        $validPlan = $this->createTestPlan('Valid Plan', 30);
        $validPlan->setValid(true);

        $invalidPlan = $this->createTestPlan('Invalid Plan', 30);
        $invalidPlan->setValid(false);

        $nullValidPlan = $this->createTestPlan('Null Valid Plan', 30);
        $nullValidPlan->setValid(null);

        $this->repository->save($validPlan);
        $this->repository->save($invalidPlan);
        $this->repository->save($nullValidPlan);

        $validPlans = $this->repository->findBy(['valid' => true]);
        $this->assertCount(1, $validPlans);
        $this->assertSame('Valid Plan', $validPlans[0]->getName());

        $invalidPlans = $this->repository->findBy(['valid' => false]);
        $this->assertCount(1, $invalidPlans);
        $this->assertSame('Invalid Plan', $invalidPlans[0]->getName());

        $nullPlans = $this->repository->findBy(['valid' => null]);
        $this->assertCount(1, $nullPlans);
        $this->assertSame('Null Valid Plan', $nullPlans[0]->getName());
    }

    public function testFindOneByReturnsNullWhenNoMatch(): void
    {
        $result = $this->repository->findOneBy(['name' => 'Non-existent Plan']);
        $this->assertNull($result);
    }

    public function testFindOneByReturnsFirstMatchingEntity(): void
    {
        $this->clearDatabase();

        $plan1 = $this->createTestPlan('Test Plan', 30);
        $plan2 = $this->createTestPlan('Another Plan', 30);

        $this->repository->save($plan1);
        $this->repository->save($plan2);

        $found = $this->repository->findOneBy(['periodDay' => 30]);
        $this->assertInstanceOf(Plan::class, $found);
        $this->assertSame(30, $found->getPeriodDay());
    }

    public function testFindOneByWithNullableField(): void
    {
        $this->clearDatabase();

        $planWithDescription = $this->createTestPlan('Plan With Desc', 30);
        $planWithDescription->setDescription('Test description');

        $planWithoutDescription = $this->createTestPlan('Plan Without Desc', 30);
        $planWithoutDescription->setDescription(null);

        $this->repository->save($planWithDescription);
        $this->repository->save($planWithoutDescription);

        $foundWithDesc = $this->repository->findOneBy(['description' => 'Test description']);
        $this->assertInstanceOf(Plan::class, $foundWithDesc);
        $this->assertSame('Plan With Desc', $foundWithDesc->getName());

        $foundWithoutDesc = $this->repository->findOneBy(['description' => null]);
        $this->assertInstanceOf(Plan::class, $foundWithoutDesc);
        $this->assertSame('Plan Without Desc', $foundWithoutDesc->getName());
    }

    public function testCountReturnsZeroWhenNoEntities(): void
    {
        $this->clearDatabase();
        $count = $this->repository->count([]);
        $this->assertSame(0, $count);
    }

    public function testCountReturnsCorrectTotal(): void
    {
        $this->clearDatabase();

        $plan1 = $this->createTestPlan('Plan 1', 30);
        $plan2 = $this->createTestPlan('Plan 2', 60);
        $plan3 = $this->createTestPlan('Plan 3', 90);

        $this->repository->save($plan1);
        $this->repository->save($plan2);
        $this->repository->save($plan3);

        $totalCount = $this->repository->count([]);
        $this->assertSame(3, $totalCount);

        $monthlyCount = $this->repository->count(['periodDay' => 30]);
        $this->assertSame(1, $monthlyCount);
    }

    public function testCountWithNullableFields(): void
    {
        $this->clearDatabase();

        $validPlan = $this->createTestPlan('Valid Plan', 30);
        $validPlan->setValid(true);

        $invalidPlan = $this->createTestPlan('Invalid Plan', 30);
        $invalidPlan->setValid(false);

        $nullValidPlan = $this->createTestPlan('Null Valid Plan', 30);
        $nullValidPlan->setValid(null);

        $this->repository->save($validPlan);
        $this->repository->save($invalidPlan);
        $this->repository->save($nullValidPlan);

        $validCount = $this->repository->count(['valid' => true]);
        $this->assertSame(1, $validCount);

        $invalidCount = $this->repository->count(['valid' => false]);
        $this->assertSame(1, $invalidCount);

        $nullCount = $this->repository->count(['valid' => null]);
        $this->assertSame(1, $nullCount);
    }

    public function testSavePeresistsNewEntity(): void
    {
        $this->clearDatabase();

        $plan = $this->createTestPlan();
        $this->assertSame(0, $plan->getId());

        $this->repository->save($plan);
        $this->assertGreaterThan(0, $plan->getId());

        $found = $this->repository->find($plan->getId());
        $this->assertInstanceOf(Plan::class, $found);
        $this->assertSame($plan->getId(), $found->getId());
    }

    public function testSaveWithoutFlushDoesNotPersistImmediately(): void
    {
        $this->clearDatabase();

        $plan = $this->createTestPlan();
        $this->repository->save($plan, false);

        // Should not be findable yet
        $found = $this->repository->find($plan->getId());
        $this->assertNull($found);

        // Manual flush should make it findable
        self::getEntityManager()->flush();
        $found = $this->repository->find($plan->getId());
        $this->assertInstanceOf(Plan::class, $found);
    }

    public function testSaveUpdatesExistingEntity(): void
    {
        $plan = $this->createTestPlan('Original Name', 30);
        $this->repository->save($plan);

        $plan->setName('Updated Name');
        $plan->setPeriodDay(60);
        $this->repository->save($plan);

        $found = $this->repository->find($plan->getId());
        $this->assertNotNull($found);
        $this->assertInstanceOf(Plan::class, $found);
        $this->assertSame('Updated Name', $found->getName());
        $this->assertSame(60, $found->getPeriodDay());
    }

    public function testRemoveDeletesEntity(): void
    {
        $plan = $this->createTestPlan();
        $this->repository->save($plan);
        $planId = $plan->getId();

        $this->repository->remove($plan);
        $found = $this->repository->find($planId);
        $this->assertNull($found);
    }

    public function testRemoveWithoutFlushDoesNotDeleteImmediately(): void
    {
        $plan = $this->createTestPlan();
        $this->repository->save($plan);
        $planId = $plan->getId();

        $this->repository->remove($plan, false);

        // Should still be findable
        $found = $this->repository->find($planId);
        $this->assertInstanceOf(Plan::class, $found);

        // Manual flush should delete it
        self::getEntityManager()->flush();
        $found = $this->repository->find($planId);
        $this->assertNull($found);
    }

    public function testEntityRelationshipsWithEquities(): void
    {
        $this->clearDatabase();

        $plan = $this->createTestPlan();
        $this->repository->save($plan);

        // Test empty equities collection
        $equities = $plan->getEquities();
        $this->assertCount(0, $equities);

        // Create and associate equity
        $equity = new Equity();
        $equity->setName('Test Equity');
        $equity->setType('feature');
        $equity->setValue('100');

        self::getEntityManager()->persist($equity);
        self::getEntityManager()->flush();

        $plan->addEquity($equity);
        $this->repository->save($plan);

        $foundPlan = $this->repository->find($plan->getId());
        $this->assertNotNull($foundPlan);
        $this->assertInstanceOf(Plan::class, $foundPlan);
        $this->assertCount(1, $foundPlan->getEquities());
        $firstEquity = $foundPlan->getEquities()->first();
        $this->assertInstanceOf(Equity::class, $firstEquity);
        $this->assertSame('Test Equity', $firstEquity->getName());
    }

    public function testEntityRelationshipsWithRecords(): void
    {
        $this->clearDatabase();

        $plan = $this->createTestPlan();
        $this->repository->save($plan);

        // Test empty records collection
        $records = $plan->getRecords();
        $this->assertCount(0, $records);

        // Create and associate record
        $user = $this->createNormalUser();
        $record = new Record();
        $record->setUser($user);
        $record->setValid(true);
        $record->setActiveTime(new \DateTimeImmutable());
        $record->setExpireTime(new \DateTimeImmutable('+30 days'));
        $record->setStatus(SubscribeStatus::ACTIVE);

        $plan->addRecord($record);
        self::getEntityManager()->persist($record);
        $this->repository->save($plan);

        $foundPlan = $this->repository->find($plan->getId());
        $this->assertNotNull($foundPlan);
        $this->assertInstanceOf(Plan::class, $foundPlan);
        $this->assertCount(1, $foundPlan->getRecords());
        $firstRecord = $foundPlan->getRecords()->first();
        $this->assertInstanceOf(Record::class, $firstRecord);
        $recordPlan = $firstRecord->getPlan();
        $this->assertNotNull($recordPlan);
        $this->assertSame($plan->getId(), $recordPlan->getId());
    }

    public function testDatabaseConstraintViolationHandling(): void
    {
        $this->expectException(Exception::class);

        $plan = new Plan();
        // Missing required name field should cause constraint violation
        $plan->setPeriodDay(30);
        $plan->setRenewCount(0);

        $this->repository->save($plan);
    }

    public function testFindByComplexCriteria(): void
    {
        $this->clearDatabase();

        $plan1 = $this->createTestPlan('Premium Plan', 365);
        $plan1->setRenewCount(5);
        $plan1->setValid(true);

        $plan2 = $this->createTestPlan('Basic Plan', 30);
        $plan2->setRenewCount(1);
        $plan2->setValid(true);

        $plan3 = $this->createTestPlan('Trial Plan', 7);
        $plan3->setRenewCount(0);
        $plan3->setValid(false);

        $this->repository->save($plan1);
        $this->repository->save($plan2);
        $this->repository->save($plan3);

        // Find valid plans with renewable counts > 0
        $renewablePlans = $this->repository->findBy(['valid' => true]);
        $renewableCount = 0;
        foreach ($renewablePlans as $plan) {
            $this->assertInstanceOf(Plan::class, $plan);
            if ($plan->getRenewCount() > 0) {
                ++$renewableCount;
            }
        }
        $this->assertSame(2, $renewableCount);

        // Find long-term plans (> 30 days)
        $allPlans = $this->repository->findAll();
        $longTermCount = 0;
        foreach ($allPlans as $plan) {
            $this->assertInstanceOf(Plan::class, $plan);
            if ($plan->getPeriodDay() > 30) {
                ++$longTermCount;
            }
        }
        $this->assertSame(1, $longTermCount);
    }

    public function testRepositoryMethodsWithOrderBy(): void
    {
        $this->clearDatabase();

        $plan1 = $this->createTestPlan('C Plan', 90);
        $plan2 = $this->createTestPlan('A Plan', 30);
        $plan3 = $this->createTestPlan('B Plan', 60);

        $this->repository->save($plan1);
        $this->repository->save($plan2);
        $this->repository->save($plan3);

        // Test ascending order by name
        $ascPlans = $this->repository->findBy([], ['name' => 'ASC']);
        $this->assertCount(3, $ascPlans);
        $this->assertInstanceOf(Plan::class, $ascPlans[0]);
        $this->assertSame('A Plan', $ascPlans[0]->getName());
        $this->assertInstanceOf(Plan::class, $ascPlans[1]);
        $this->assertSame('B Plan', $ascPlans[1]->getName());
        $this->assertInstanceOf(Plan::class, $ascPlans[2]);
        $this->assertSame('C Plan', $ascPlans[2]->getName());

        // Test descending order by periodDay
        $descPlans = $this->repository->findBy([], ['periodDay' => 'DESC']);
        $this->assertCount(3, $descPlans);
        $this->assertInstanceOf(Plan::class, $descPlans[0]);
        $this->assertSame(90, $descPlans[0]->getPeriodDay());
        $this->assertInstanceOf(Plan::class, $descPlans[1]);
        $this->assertSame(60, $descPlans[1]->getPeriodDay());
        $this->assertInstanceOf(Plan::class, $descPlans[2]);
        $this->assertSame(30, $descPlans[2]->getPeriodDay());
    }

    private function createTestPlan(string $name = 'Test Plan', int $periodDay = 30): Plan
    {
        $plan = new Plan();
        $plan->setName($name);
        $plan->setPeriodDay($periodDay);
        $plan->setRenewCount(0);
        $plan->setValid(false);

        return $plan;
    }

    public function testQueryingNullableDescriptionField(): void
    {
        $this->clearDatabase();

        $planWithDesc = $this->createTestPlan('Plan With Description');
        $planWithDesc->setDescription('Test description');

        $planWithoutDesc = $this->createTestPlan('Plan Without Description');
        $planWithoutDesc->setDescription(null);

        $this->repository->save($planWithDesc);
        $this->repository->save($planWithoutDesc);

        // Test finding by non-null description
        $withDescResult = $this->repository->findBy(['description' => 'Test description']);
        $this->assertCount(1, $withDescResult);
        $this->assertInstanceOf(Plan::class, $withDescResult[0]);
        $this->assertSame('Plan With Description', $withDescResult[0]->getName());

        // Test finding by null description
        $withoutDescResult = $this->repository->findBy(['description' => null]);
        $this->assertCount(1, $withoutDescResult);
        $this->assertInstanceOf(Plan::class, $withoutDescResult[0]);
        $this->assertSame('Plan Without Description', $withoutDescResult[0]->getName());

        // Test counting null descriptions
        $nullDescCount = $this->repository->count(['description' => null]);
        $this->assertSame(1, $nullDescCount);
    }

    public function testQueryingNullableValidField(): void
    {
        $this->clearDatabase();

        $validPlan = $this->createTestPlan('Valid Plan');
        $validPlan->setValid(true);

        $invalidPlan = $this->createTestPlan('Invalid Plan');
        $invalidPlan->setValid(false);

        $nullValidPlan = $this->createTestPlan('Null Valid Plan');
        $nullValidPlan->setValid(null);

        $this->repository->save($validPlan);
        $this->repository->save($invalidPlan);
        $this->repository->save($nullValidPlan);

        // Test finding by valid = true
        $validResult = $this->repository->findBy(['valid' => true]);
        $this->assertCount(1, $validResult);
        $this->assertInstanceOf(Plan::class, $validResult[0]);
        $this->assertSame('Valid Plan', $validResult[0]->getName());

        // Test finding by valid = false
        $invalidResult = $this->repository->findBy(['valid' => false]);
        $this->assertCount(1, $invalidResult);
        $this->assertInstanceOf(Plan::class, $invalidResult[0]);
        $this->assertSame('Invalid Plan', $invalidResult[0]->getName());

        // Test finding by valid = null
        $nullResult = $this->repository->findBy(['valid' => null]);
        $this->assertCount(1, $nullResult);
        $this->assertInstanceOf(Plan::class, $nullResult[0]);
        $this->assertSame('Null Valid Plan', $nullResult[0]->getName());

        // Test counting null valid fields
        $nullValidCount = $this->repository->count(['valid' => null]);
        $this->assertSame(1, $nullValidCount);
    }

    private function clearDatabase(): void
    {
        $connection = self::getEntityManager()->getConnection();

        // Clear tables only if they exist
        try {
            $connection->executeStatement('DELETE FROM ims_subscription_record');
        } catch (\Exception $e) {
            // Table doesn't exist, ignore
        }

        try {
            $connection->executeStatement('DELETE FROM ims_subscription_equity_plan');
        } catch (\Exception $e) {
            // Table doesn't exist, ignore
        }

        $connection->executeStatement('DELETE FROM ims_subscription_plan');
    }

    /**
     * 测试 findOneBy valid 可空字段查询
     */

    /**
     * 测试 findOneBy description 可空字段查询
     */

    /**
     * 测试 count valid 可空字段
     */

    /**
     * 测试 count description 可空字段
     */

    /**
     * 测试 findBy valid 可空字段查询
     */

    /**
     * 测试 findBy description 可空字段查询
     */
    protected function createNewEntity(): object
    {
        $entity = new Plan();
        $entity->setName('Test Plan ' . uniqid());
        $entity->setDescription('Test plan description');
        $entity->setPeriodDay(30);
        $entity->setRenewCount(0);
        $entity->setValid(true);

        return $entity;
    }

    /**
     * @return PlanRepository
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}
