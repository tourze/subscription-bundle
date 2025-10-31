# Subscription Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/subscription-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/subscription-bundle)
[![PHP Version](https://img.shields.io/packagist/php-v/tourze/subscription-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/subscription-bundle)
[![Build Status](https://img.shields.io/github/actions/workflow/status/tourze/php-monorepo/test.yml?branch=master&style=flat-square)](https://github.com/tourze/php-monorepo/actions)
[![Coverage Status](https://img.shields.io/coveralls/github/tourze/php-monorepo/master.svg?style=flat-square)](https://coveralls.io/github/tourze/php-monorepo?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/subscription-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/subscription-bundle)
[![License](https://img.shields.io/github/license/tourze/php-monorepo.svg?style=flat-square)](https://github.com/tourze/php-monorepo/blob/master/LICENSE)

A comprehensive subscription management bundle for Symfony applications, providing 
subscription plans, user records, equity management, and resource tracking capabilities.

## Table of Contents

- [Features](#features)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
  - [Creating Subscription Plans](#creating-subscription-plans)
  - [Managing User Subscriptions](#managing-user-subscriptions)
  - [Tracking Resource Usage](#tracking-resource-usage)
- [Advanced Usage](#advanced-usage)
  - [Repository Methods](#repository-methods)
  - [Event Handling](#event-handling)
  - [Custom Validation](#custom-validation)
- [Entities](#entities)
- [Enums](#enums)
- [Repositories](#repositories)
- [Requirements](#requirements)
- [Dependencies](#dependencies)
- [License](#license)

## Features

- **Subscription Plans**: Create and manage different subscription plans with custom periods and renewal options
- **User Records**: Track user subscriptions with activation and expiration times
- **Equity Management**: Define and manage user benefits/entitlements (e.g., lottery chances, traffic allowances)
- **Resource Tracking**: Monitor resource usage and limits
- **Status Management**: Track subscription status (active/expired)
- **Doctrine Integration**: Full ORM support with proper relationships and indexing

## Installation

```bash
composer require tourze/subscription-bundle
```

## Configuration

Add the bundle to your `config/bundles.php`:

```php
return [
    // ...
    Tourze\SubscriptionBundle\SubscriptionBundle::class => ['all' => true],
];
```

## Usage

### Creating Subscription Plans

```php
use Tourze\SubscriptionBundle\Entity\Plan;
use Tourze\SubscriptionBundle\Entity\Equity;

// Create a basic plan
$plan = new Plan();
$plan->setName('Premium Monthly')
     ->setDescription('Premium subscription with monthly billing')
     ->setPeriodDay(30)
     ->setRenewCount(12)
     ->setValid(true);

// Create equity/benefits
$equity = new Equity();
$equity->setName('Monthly Traffic')
       ->setType('traffic')
       ->setValue('100000000000') // 100GB in bytes
       ->setDescription('Monthly traffic allowance');

// Associate equity with plan
$plan->addEquity($equity);

$entityManager->persist($plan);
$entityManager->persist($equity);
$entityManager->flush();
```

### Managing User Subscriptions

```php
use Tourze\SubscriptionBundle\Entity\Record;
use Tourze\SubscriptionBundle\Enum\SubscribeStatus;

// Create subscription record for user
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

### Tracking Resource Usage

```php
use Tourze\SubscriptionBundle\Entity\Usage;

// Track resource usage
$usage = new Usage();
$usage->setUser($user)
      ->setRecord($record)
      ->setEquity($equity)
      ->setValue('1000000000') // 1GB used in bytes
      ->setDate(new \DateTimeImmutable())
      ->setTime('1430'); // 14:30

$entityManager->persist($usage);
$entityManager->flush();
```

## Advanced Usage

### Repository Methods

```php
// Find active subscriptions for a user
$activeRecords = $recordRepository->findBy([
    'user' => $user,
    'valid' => true
]);

// Get plans with specific equity types
$plansWithTraffic = $planRepository->createQueryBuilder('p')
    ->join('p.equities', 'e')
    ->where('e.type = :type')
    ->setParameter('type', 'traffic')
    ->getQuery()
    ->getResult();
```

### Event Handling

```php
// Listen for subscription events (if needed)
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
        // Handle subscription activation
    }

    public function onSubscriptionExpired($event): void
    {
        // Handle subscription expiration
    }
}
```

### Custom Validation

```php
// Add custom validation to entities
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class Plan
{
    #[Assert\Callback]
    public function validatePeriod(ExecutionContextInterface $context): void
    {
        if ($this->periodDay < 1) {
            $context->buildViolation('Period must be at least 1 day')
                ->atPath('periodDay')
                ->addViolation();
        }
    }
}
```

## Entities

### Plan
- Represents subscription plans with pricing and duration
- Contains equities/benefits associated with the plan
- Supports renewal limits and validation

### Record
- Tracks individual user subscriptions
- Links users to specific plans with activation/expiration dates
- Manages subscription status

### Equity
- Defines benefits/entitlements (traffic, features, etc.)
- Can be associated with multiple plans
- Supports various types and values

### Resource
- Represents consumable resources
- Tracks limits and descriptions

### Usage
- Monitors resource consumption by users
- Tracks used vs. allocated amounts

## Enums

### SubscribeStatus
- `ACTIVE`: Active subscription
- `EXPIRED`: Expired subscription

## Repositories

All entities come with dedicated repositories for advanced querying:

- `PlanRepository`
- `RecordRepository`
- `EquityRepository`
- `ResourceRepository`
- `UsageRepository`

## Requirements

- PHP 8.1+
- Symfony 6.4+
- Doctrine ORM 3.0+

## Dependencies

This bundle depends on several other Tourze bundles:
- `tourze/doctrine-indexed-bundle` - Database indexing support
- `tourze/doctrine-timestamp-bundle` - Automatic timestamp management
- `tourze/doctrine-track-bundle` - Entity change tracking
- `tourze/doctrine-user-bundle` - User management integration
- `tourze/enum-extra` - Enhanced enum functionality

## License

This bundle is licensed under the MIT License.