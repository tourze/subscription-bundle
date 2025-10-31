<?php

namespace Tourze\SubscriptionBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\SubscriptionBundle\Repository\EquityRepository;

/**
 * 权益应该是一些可以消耗的资源，例如抽奖次数/流量
 */
#[ORM\Table(name: 'ims_subscription_equity', options: ['comment' => '权益'])]
#[ORM\Entity(repositoryClass: EquityRepository::class)]
class Equity implements \Stringable
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

    /** @var Collection<int, Plan> */
    #[Ignore]
    #[ORM\ManyToMany(targetEntity: Plan::class, mappedBy: 'equities', fetch: 'EXTRA_LAZY')]
    private Collection $plans;

    #[ORM\Column(length: 120, options: ['comment' => '名称'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 120)]
    private string $name;

    #[ORM\Column(length: 20, options: ['comment' => '类型'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    private string $type;

    #[ORM\Column(type: Types::BIGINT, options: ['comment' => '数值'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    private string $value = '0';

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '描述'])]
    #[Assert\Length(max: 65535)]
    private ?string $description = null;

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

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        if (0 === $this->getId()) {
            return 'new Equity';
        }

        return "{$this->getName()}";
    }
}
