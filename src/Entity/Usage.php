<?php

namespace SubscriptionBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use SubscriptionBundle\Repository\UsageRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\EasyAdmin\Attribute\Action\Creatable;
use Tourze\EasyAdmin\Attribute\Action\Deletable;
use Tourze\EasyAdmin\Attribute\Action\Editable;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;

#[AsPermission(title: '资源消耗')]
#[Creatable]
#[Editable]
#[Deletable]
#[ORM\Table(name: 'ims_subscription_usage', options: ['comment' => '资源消耗'])]
#[ORM\Entity(repositoryClass: UsageRepository::class)]
class Usage
{
    use TimestampableAware;
    #[ListColumn(order: -1)]
    #[ExportColumn]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    #[ListColumn(title: '用户')]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserInterface $user = null;

    #[ListColumn(title: '订阅记录')]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Record $record = null;

    #[ListColumn(title: '权益名')]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Equity $equity = null;

    #[ListColumn]
    #[ORM\Column(type: Types::DATE_MUTABLE, options: ['comment' => '日期'])]
    private ?\DateTimeInterface $date = null;

    #[ListColumn]
    #[ORM\Column(length: 4, options: ['comment' => '时分'])]
    private ?string $time = null;

    #[ListColumn]
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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
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
    }}
