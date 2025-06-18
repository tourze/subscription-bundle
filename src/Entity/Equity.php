<?php

namespace SubscriptionBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use SubscriptionBundle\Repository\EquityRepository;
use Symfony\Component\Serializer\Attribute\Ignore;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Tourze\EasyAdmin\Attribute\Action\Creatable;
use Tourze\EasyAdmin\Attribute\Action\Deletable;
use Tourze\EasyAdmin\Attribute\Action\Editable;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Field\FormField;
use Tourze\EasyAdmin\Attribute\Filter\Keyword;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;

/**
 * 权益应该是一些可以消耗的资源，例如抽奖次数/流量
 */
#[AsPermission(title: '权益')]
#[Creatable]
#[Editable]
#[Deletable]
#[ORM\Table(name: 'ims_subscription_equity', options: ['comment' => '权益'])]
#[ORM\Entity(repositoryClass: EquityRepository::class)]
class Equity implements \Stringable
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

    #[Ignore]
    #[ORM\ManyToMany(targetEntity: Plan::class, inversedBy: 'equities', fetch: 'EXTRA_LAZY')]
    private Collection $plans;

    #[Keyword]
    #[ListColumn]
    #[FormField(span: 8)]
    #[ORM\Column(length: 120, options: ['comment' => '名称'])]
    private string $name;

    #[ListColumn(sorter: true)]
    #[FormField(span: 8)]
    #[ORM\Column(length: 20, options: ['comment' => '类型'])]
    private ?string $type;

    #[ListColumn(sorter: true)]
    #[FormField(span: 8)]
    #[ORM\Column(type: Types::BIGINT, options: ['comment' => '数值'])]
    private ?string $value = '0';

    #[FormField]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '描述'])]
    private ?string $description = null;

    #[CreatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '创建人'])]
    private ?string $createdBy = null;

    #[UpdatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '更新人'])]
    private ?string $updatedBy = null;

    public function __construct()
    {
        $this->plans = new ArrayCollection();
    }

    /**
     * @return Collection<int, Plan>
     */
    public function getPlans(): Collection
    {
        return $this->plans;
    }

    public function addPlan(Plan $plan): static
    {
        if (!$this->plans->contains($plan)) {
            $this->plans->add($plan);
        }

        return $this;
    }

    public function removePlan(Plan $plan): static
    {
        $this->plans->removeElement($plan);

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

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

    public function setCreatedBy(?string $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    public function setUpdatedBy(?string $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function getUpdatedBy(): ?string
    {
        return $this->updatedBy;
    }public function __toString(): string
    {
        if (!$this->getId()) {
            return '';
        }

        return "{$this->getName()}";
    }
}
