# Subscription Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/subscription-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/subscription-bundle)
[![PHP Version](https://img.shields.io/packagist/php-v/tourze/subscription-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/subscription-bundle)
[![Build Status](https://img.shields.io/github/actions/workflow/status/tourze/php-monorepo/test.yml?branch=master&style=flat-square)](https://github.com/tourze/php-monorepo/actions)
[![Coverage Status](https://img.shields.io/coveralls/github/tourze/php-monorepo/master.svg?style=flat-square)](https://coveralls.io/github/tourze/php-monorepo?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/subscription-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/subscription-bundle)
[![License](https://img.shields.io/github/license/tourze/php-monorepo.svg?style=flat-square)](https://github.com/tourze/php-monorepo/blob/master/LICENSE)

一个为 Symfony 应用程序设计的全面订阅管理包，提供订阅计划、用户记录、权益管理和资源跟踪功能。

## 目录

- [特性](#特性)
- [安装](#安装)
- [配置](#配置)
- [使用方法](#使用方法)
  - [创建订阅计划](#创建订阅计划)
  - [管理用户订阅](#管理用户订阅)
  - [跟踪资源使用](#跟踪资源使用)
- [高级用法](#高级用法)
  - [仓储方法](#仓储方法)
  - [事件处理](#事件处理)
  - [自定义验证](#自定义验证)
- [实体](#实体)
- [枚举](#枚举)
- [仓储](#仓储)
- [要求](#要求)
- [依赖](#依赖)
- [许可证](#许可证)

## 特性

- **订阅计划**: 创建和管理不同的订阅计划，支持自定义周期和续订选项
- **用户记录**: 跟踪用户订阅，包含激活和过期时间
- **权益管理**: 定义和管理用户权益/福利（如抽奖次数、流量配额）
- **资源跟踪**: 监控资源使用情况和限制
- **状态管理**: 跟踪订阅状态（活跃/过期）
- **Doctrine 集成**: 完整的 ORM 支持，包含适当的关系和索引

## 安装

```bash
composer require tourze/subscription-bundle
```

## 配置

将此包添加到您的 `config/bundles.php` 文件中：

```php
return [
    // ...
    Tourze\SubscriptionBundle\SubscriptionBundle::class => ['all' => true],
];
```

## 使用方法

### 创建订阅计划

```php
use Tourze\SubscriptionBundle\Entity\Plan;
use Tourze\SubscriptionBundle\Entity\Equity;

// 创建基础计划
$plan = new Plan();
$plan->setName('高级月度订阅')
     ->setDescription('高级订阅，月度计费')
     ->setPeriodDay(30)
     ->setRenewCount(12)
     ->setValid(true);

// 创建权益/福利
$equity = new Equity();
$equity->setName('月度流量')
       ->setType('traffic')
       ->setValue('100000000000') // 100GB 字节数
       ->setDescription('月度流量配额');

// 将权益与计划关联
$plan->addEquity($equity);

$entityManager->persist($plan);
$entityManager->persist($equity);
$entityManager->flush();
```

### 管理用户订阅

```php
use Tourze\SubscriptionBundle\Entity\Record;
use Tourze\SubscriptionBundle\Enum\SubscribeStatus;

// 为用户创建订阅记录
$record = new Record();
$record->setPlan($plan)
       ->setUser($user)
       ->setActiveTime(new \DateTimeImmutable())
       ->setExpireTime(new \DateTimeImmutable('+30 days'))
       ->setStatus(SubscribeStatus::ACTIVE)
       ->setValid(true);

$entityManager->persist($record);
$entityManager->flush();
```

### 跟踪资源使用

```php
use Tourze\SubscriptionBundle\Entity\Usage;

// 跟踪资源使用情况
$usage = new Usage();
$usage->setUser($user)
      ->setRecord($record)
      ->setEquity($equity)
      ->setValue('1000000000') // 已使用 1GB（字节数）
      ->setDate(new \DateTimeImmutable())
      ->setTime('1430'); // 14:30

$entityManager->persist($usage);
$entityManager->flush();
```

## 高级用法

### 仓储方法

```php
// 查找用户的活跃订阅
$activeRecords = $recordRepository->findBy([
    'user' => $user,
    'valid' => true
]);

// 获取具有特定权益类型的计划
$plansWithTraffic = $planRepository->createQueryBuilder('p')
    ->join('p.equities', 'e')
    ->where('e.type = :type')
    ->setParameter('type', 'traffic')
    ->getQuery()
    ->getResult();
```

### 事件处理

```php
// 监听订阅事件（如果需要）
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SubscriptionEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            'subscription.activated' => 'onSubscriptionActivated',
            'subscription.expired' => 'onSubscriptionExpired',
        ];
    }

    public function onSubscriptionActivated($event): void
    {
        // 处理订阅激活
    }

    public function onSubscriptionExpired($event): void
    {
        // 处理订阅过期
    }
}
```

### 自定义验证

```php
// 为实体添加自定义验证
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class Plan
{
    #[Assert\Callback]
    public function validatePeriod(ExecutionContextInterface $context): void
    {
        if ($this->periodDay < 1) {
            $context->buildViolation('周期至少为 1 天')
                ->atPath('periodDay')
                ->addViolation();
        }
    }
}
```

## 实体

### Plan（计划）
- 表示订阅计划，包含价格和持续时间
- 包含与计划关联的权益/福利
- 支持续订限制和验证

### Record（记录）
- 跟踪个人用户订阅
- 将用户与特定计划关联，包含激活/过期日期
- 管理订阅状态

### Equity（权益）
- 定义福利/权益（流量、功能等）
- 可与多个计划关联
- 支持各种类型和值

### Resource（资源）
- 表示可消耗资源
- 跟踪限制和描述

### Usage（使用情况）
- 监控用户资源消耗
- 跟踪已使用与分配的数量

## 枚举

### SubscribeStatus（订阅状态）
- `ACTIVE`: 活跃订阅
- `EXPIRED`: 过期订阅

## 仓储

所有实体都配有专用仓储进行高级查询：

- `PlanRepository`
- `RecordRepository`
- `EquityRepository`
- `ResourceRepository`
- `UsageRepository`

## 要求

- PHP 8.1+
- Symfony 6.4+
- Doctrine ORM 3.0+

## 依赖

此包依赖于其他几个 Tourze 包：
- `tourze/doctrine-indexed-bundle` - 数据库索引支持
- `tourze/doctrine-timestamp-bundle` - 自动时间戳管理
- `tourze/doctrine-track-bundle` - 实体变更跟踪
- `tourze/doctrine-user-bundle` - 用户管理集成
- `tourze/enum-extra` - 增强的枚举功能

## 许可证

此包使用 MIT 许可证。
