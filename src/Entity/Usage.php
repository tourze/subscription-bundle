<?php

namespace Tourze\SubscriptionBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\SubscriptionBundle\Repository\UsageRepository;

#[ORM\Table(name: 'ims_subscription_usage', options: ['comment' => '资源消耗'])]
#[ORM\Entity(repositoryClass: UsageRepository::class)]
class Usage implements \Stringable
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

    #[ORM\ManyToOne(targetEntity: UserInterface::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserInterface $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Record $record = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Equity $equity = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, options: ['comment' => '日期'])]
    #[Assert\NotNull]
    private ?\DateTimeImmutable $date = null;

    #[ORM\Column(length: 4, options: ['comment' => '时分'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 4)]
    private ?string $time = null;

    #[ORM\Column(type: Types::BIGINT, options: ['comment' => '消耗数量'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    private ?string $value = null;

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): void
    {
        $this->user = $user;
    }

    public function getRecord(): ?Record
    {
        return $this->record;
    }

    public function setRecord(?Record $record): void
    {
        $this->record = $record;
    }

    public function getEquity(): ?Equity
    {
        return $this->equity;
    }

    public function setEquity(?Equity $equity): void
    {
        $this->equity = $equity;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): void
    {
        $this->date = $date;
    }

    public function getTime(): ?string
    {
        return $this->time;
    }

    public function setTime(string $time): void
    {
        $this->time = $time;
    }

    public function __toString(): string
    {
        return sprintf('Usage[%s]', 0 === $this->id ? 'new' : $this->id);
    }
}
