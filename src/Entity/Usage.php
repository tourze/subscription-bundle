<?php

namespace Tourze\SubscriptionBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\SubscriptionBundle\Repository\UsageRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

#[ORM\Table(name: 'ims_subscription_usage', options: ['comment' => '资源消耗'])]
#[ORM\Entity(repositoryClass: UsageRepository::class)]
class Usage implements \Stringable
{
    use TimestampableAware;
    use BlameableAware;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserInterface $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Record $record = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Equity $equity = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, options: ['comment' => '日期'])]
    private ?\DateTimeImmutable $date = null;

    #[ORM\Column(length: 4, options: ['comment' => '时分'])]
    private ?string $time = null;

    #[ORM\Column(type: Types::BIGINT, options: ['comment' => '消耗数量'])]
    private ?string $value = null;

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getRecord(): ?Record
    {
        return $this->record;
    }

    public function setRecord(?Record $record): static
    {
        $this->record = $record;

        return $this;
    }

    public function getEquity(): ?Equity
    {
        return $this->equity;
    }

    public function setEquity(?Equity $equity): static
    {
        $this->equity = $equity;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getTime(): ?string
    {
        return $this->time;
    }

    public function setTime(string $time): static
    {
        $this->time = $time;

        return $this;
    }

    public function __toString(): string
    {
        return sprintf('Usage[%s]', $this->id ?? 'new');
    }
}
