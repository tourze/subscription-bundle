<?php

namespace Tourze\SubscriptionBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\SubscriptionBundle\Repository\PlanRepository;

#[ORM\Table(name: 'ims_subscription_plan', options: ['comment' => '订阅计划'])]
#[ORM\Entity(repositoryClass: PlanRepository::class)]
class Plan implements \Stringable
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

    public function setValid(?bool $valid): void
    {
        $this->valid = $valid;
    }

    #[ORM\Column(length: 64, options: ['comment' => '名称'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '描述'])]
    #[Assert\Length(max: 65535)]
    private ?string $description = null;

    #[ORM\Column(options: ['comment' => '生效天数'])]
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    private int $periodDay = 30;

    #[ORM\Column(options: ['comment' => '可续订次数'])]
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    private int $renewCount = 0;

    /** @var Collection<int, Equity> */
    #[ORM\ManyToMany(targetEntity: Equity::class, inversedBy: 'plans', fetch: 'EXTRA_LAZY')]
    #[ORM\JoinTable(name: 'ims_subscription_equity_plan')]
    private Collection $equities;

    /** @var Collection<int, Record> */
    #[Ignore]
    #[ORM\OneToMany(mappedBy: 'plan', targetEntity: Record::class, orphanRemoval: true)]
    private Collection $records;

    public function __construct()
    {
        $this->records = new ArrayCollection();
        $this->equities = new ArrayCollection();
    }

    /**
     * @return Collection<int, Record>
     */
    public function getRecords(): Collection
    {
        return $this->records;
    }

    public function addRecord(Record $record): static
    {
        if (!$this->records->contains($record)) {
            $this->records->add($record);
            $record->setPlan($this);
        }

        return $this;
    }

    public function removeRecord(Record $record): static
    {
        if ($this->records->removeElement($record)) {
            // set the owning side to null (unless already changed)
            if ($record->getPlan() === $this) {
                $record->setPlan(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Equity>
     */
    public function getEquities(): Collection
    {
        return $this->equities;
    }

    public function addEquity(Equity $feature): static
    {
        if (!$this->equities->contains($feature)) {
            $this->equities->add($feature);
            $feature->addPlan($this);
        }

        return $this;
    }

    public function removeEquity(Equity $feature): static
    {
        if ($this->equities->removeElement($feature)) {
            $feature->removePlan($this);
        }

        return $this;
    }

    public function getName(): ?string
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

    public function getPeriodDay(): int
    {
        return $this->periodDay;
    }

    public function setPeriodDay(int $periodDay): void
    {
        $this->periodDay = $periodDay;
    }

    public function getRenewCount(): int
    {
        return $this->renewCount;
    }

    public function setRenewCount(int $renewCount): void
    {
        $this->renewCount = $renewCount;
    }

    public function __toString(): string
    {
        return sprintf('Plan[%s]', $this->name ?? 'unnamed');
    }
}
