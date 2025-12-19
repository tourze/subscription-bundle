<?php

declare(strict_types=1);

namespace Tourze\SubscriptionBundle\Tests\Fixtures;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * 测试用简单用户对象
 */
class TestUser implements UserInterface
{
    public function __construct(
        private int $id = 1,
        private string $username = 'test_user',
        private string $email = 'test@example.com'
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials(): void
    {
        // 测试用户无需实现
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}