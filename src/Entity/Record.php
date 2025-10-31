<?php

namespace Tourze\SubscriptionBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\SubscriptionBundle\Enum\SubscribeStatus;
use Tourze\SubscriptionBundle\Repository\RecordRepository;

#[ORM\Table(name: 'ims_subscription_record', options: ['comment' => '订阅记录'])]
#[ORM\Entity(repositoryClass: RecordRepository::class)]
class Record implements \Stringable
{
    use TimestampableAware;
    use BlameableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    public function getId(): int
    {
        return $this->id;
    }

    #[IndexColumn]
    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    #[Assert\Type(type: 'bool')]
    private ?bool $valid = false;

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function getValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): void
    {
        $this->valid = $valid;
    }

    #[ORM\ManyToOne(inversedBy: 'records')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Plan $plan = null;

    #[IndexColumn]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['comment' => '激活时间'])]
    #[Assert\NotNull]
    private ?\DateTimeImmutable $activeTime = null;

    #[IndexColumn]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['comment' => '过期时间'])]
    #[Assert\NotNull]
    private ?\DateTimeImmutable $expireTime = null;

    #[ORM\ManyToOne(targetEntity: UserInterface::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserInterface $user = null;

    #[ORM\Column(type: Types::STRING, length: 20, nullable: true, enumType: SubscribeStatus::class, options: ['comment' => '订阅状态'])]
    #[Assert\NotNull]
    #[Assert\Choice(callback: [SubscribeStatus::class, 'cases'])]
    private ?SubscribeStatus $status = null;

    public function getPlan(): ?Plan
    {
        return $this->plan;
    }

    public function setPlan(?Plan $plan): void
    {
        $this->plan = $plan;
    }

    public function getActiveTime(): ?\DateTimeImmutable
    {
        return $this->activeTime;
    }

    public function setActiveTime(\DateTimeImmutable $activeTime): void
    {
        $this->activeTime = $activeTime;
    }

    public function getExpireTime(): ?\DateTimeImmutable
    {
        return $this->expireTime;
    }

    public function setExpireTime(\DateTimeImmutable $expireTime): void
    {
        $this->expireTime = $expireTime;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): void
    {
        $this->user = $user;
    }

    public function getStatus(): ?SubscribeStatus
    {
        return $this->status;
    }

    public function setStatus(?SubscribeStatus $status): void
    {
        $this->status = $status;
    }

    public function __toString(): string
    {
        return sprintf('Record[%s]', 0 === $this->id ? 'new' : $this->id);
    }
}
