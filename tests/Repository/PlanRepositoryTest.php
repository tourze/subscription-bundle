<?php

namespace Tourze\SubscriptionBundle\Tests\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Tourze\SubscriptionBundle\Entity\Plan;
use Tourze\SubscriptionBundle\Repository\PlanRepository;

class PlanRepositoryTest extends TestCase
{
    private PlanRepository $repository;
    private $managerRegistry;
    private $entityManager;

    protected function setUp(): void
    {
        // 创建模拟对象
        $this->entityManager = $this->createMock(EntityManager::class);
        $this->managerRegistry = $this->createMock(ManagerRegistry::class);
        
        // 配置模拟对象行为，不强制调用次数
        $this->managerRegistry->method('getManagerForClass')
            ->with(Plan::class)
            ->willReturn($this->entityManager);
        
        // 创建实际的Repository实例
        $this->repository = new PlanRepository($this->managerRegistry);
    }
    
    public function testConstructor_withValidRegistry(): void
    {
        // 仅测试构造函数是否正确执行
        $this->assertInstanceOf(PlanRepository::class, $this->repository);
    }
    
    /**
     * 注意：这个测试类只是演示如何进行Repository的单元测试
     * 实际的数据库操作在这里只进行模拟，不实际执行
     */
} 