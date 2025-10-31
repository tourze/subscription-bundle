<?php

declare(strict_types=1);

namespace Tourze\SubscriptionBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\SubscriptionBundle\Entity\Equity;
use Tourze\SubscriptionBundle\Entity\Record;
use Tourze\SubscriptionBundle\Entity\Usage;

/**
 * 资源消耗管理控制器
 * @extends AbstractCrudController<Usage>
 */
#[AdminCrud(routePath: '/subscription/usage', routeName: 'subscription_usage')]
#[Autoconfigure(public: true)]
final class UsageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Usage::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('资源消耗')
            ->setEntityLabelInPlural('资源消耗管理')
            ->setPageTitle('index', '资源消耗列表')
            ->setPageTitle('new', '新增资源消耗')
            ->setPageTitle('edit', '编辑资源消耗')
            ->setPageTitle('detail', '资源消耗详情')
            ->setHelp('index', '管理用户的资源消耗记录，包括权益使用情况和消耗数量')
            ->setDefaultSort(['date' => 'DESC', 'time' => 'DESC'])
            ->setSearchFields(['user', 'record.plan.name', 'equity.name', 'value'])
            ->setPaginatorPageSize(50)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        $isIndexPage = Crud::PAGE_INDEX === $pageName;

        yield IdField::new('id', 'ID')
            ->setMaxLength(9999)
            ->hideOnForm()
        ;

        yield from $this->configureUserField($isIndexPage);
        yield from $this->configureRecordField($isIndexPage);
        yield from $this->configureEquityField($isIndexPage);

        yield DateField::new('date', '日期')
            ->setRequired(!$isIndexPage)
            ->setHelp('资源消耗的日期')
        ;

        yield $this->configureTimeField($isIndexPage);
        yield $this->configureValueField($isIndexPage);

        yield DateTimeField::new('createTime', '创建时间')
            ->setFormat('yyyy-MM-dd HH:mm:ss')
            ->hideOnForm()
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->setFormat('yyyy-MM-dd HH:mm:ss')
            ->hideOnForm()
        ;
    }

    /**
     * @return iterable<AssociationField>
     */
    private function configureUserField(bool $isIndexPage): iterable
    {
        if ($isIndexPage) {
            yield AssociationField::new('user', '用户')
                ->formatValue(function ($value, Usage $entity): string {
                    $user = $entity->getUser();

                    return null !== $user ? $user->getUserIdentifier() : 'N/A';
                })
            ;
        } else {
            yield AssociationField::new('user', '用户')
                ->setRequired(true)
                ->setHelp('选择消耗资源的用户')
                ->setFormTypeOptions(['placeholder' => '请选择用户'])
                ->hideOnIndex()
            ;
        }
    }

    /**
     * @return iterable<AssociationField>
     */
    private function configureRecordField(bool $isIndexPage): iterable
    {
        if ($isIndexPage) {
            return;
        }

        yield AssociationField::new('record', '订阅记录')
            ->setRequired(true)
            ->setHelp('关联的订阅记录,确定用户的套餐信息')
            ->setFormTypeOptions([
                'placeholder' => '请选择订阅记录',
                'choice_label' => $this->getRecordChoiceLabel(...),
            ])
            ->hideOnIndex()
        ;
    }

    /**
     * @return iterable<AssociationField>
     */
    private function configureEquityField(bool $isIndexPage): iterable
    {
        if ($isIndexPage) {
            yield AssociationField::new('equity', '权益')
                ->formatValue(function ($value, Usage $entity): string {
                    $equity = $entity->getEquity();

                    return null !== $equity ? sprintf('%s (%s)', $equity->getName(), $equity->getType()) : 'N/A';
                })
            ;
        } else {
            yield AssociationField::new('equity', '权益')
                ->setRequired(true)
                ->setHelp('选择消耗的权益类型,如流量、次数等')
                ->setFormTypeOptions([
                    'placeholder' => '请选择权益',
                    'choice_label' => $this->getEquityChoiceLabel(...),
                ])
                ->hideOnIndex()
            ;
        }
    }

    private function configureTimeField(bool $isIndexPage): TextField
    {
        return TextField::new('time', '时间')
            ->setRequired(!$isIndexPage)
            ->setMaxLength(4)
            ->setHelp('24小时制时分格式,如:1430 表示 14:30')
            ->formatValue(function (?string $value) use ($isIndexPage): ?string {
                if (!$isIndexPage || null === $value || 4 !== strlen($value)) {
                    return $value;
                }

                return substr($value, 0, 2) . ':' . substr($value, 2, 2);
            })
        ;
    }

    private function configureValueField(bool $isIndexPage): TextField
    {
        return TextField::new('value', '消耗数量')
            ->setRequired(!$isIndexPage)
            ->setMaxLength(20)
            ->setHelp('消耗的数量,支持大数值')
            ->formatValue(function (?string $value) use ($isIndexPage): string {
                if (!$isIndexPage) {
                    return $value ?? '';
                }

                return number_format((float) $value);
            })
        ;
    }

    private function getRecordChoiceLabel(?Record $record): string
    {
        if (null === $record) {
            return '';
        }

        $plan = $record->getPlan();
        $user = $record->getUser();
        $planName = null !== $plan ? $plan->getName() : 'Unknown Plan';
        $userName = null !== $user ? $user->getUserIdentifier() : 'Unknown User';

        return sprintf('%s - %s', $planName, $userName);
    }

    private function getEquityChoiceLabel(?Equity $equity): string
    {
        if (null === $equity) {
            return '';
        }

        return sprintf('%s (%s)', $equity->getName(), $equity->getType());
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('user', '用户'))
            ->add(EntityFilter::new('record', '订阅记录')->setFormTypeOptions([
                'choice_label' => function (?Record $record): string {
                    if (null === $record) {
                        return '';
                    }
                    $plan = $record->getPlan();

                    return null !== $plan ? ($plan->getName() ?? 'Unknown Plan') : 'Unknown Plan';
                },
            ]))
            ->add(EntityFilter::new('equity', '权益')->setFormTypeOptions([
                'choice_label' => function (?Equity $equity): string {
                    if (null === $equity) {
                        return '';
                    }

                    return sprintf('%s (%s)', $equity->getName(), $equity->getType());
                },
            ]))
            ->add(TextFilter::new('date', '日期'))
            ->add(TextFilter::new('time', '时间'))
            ->add(TextFilter::new('value', '消耗数量'))
        ;
    }
}
