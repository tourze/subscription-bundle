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
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use Tourze\SubscriptionBundle\Entity\Record;
use Tourze\SubscriptionBundle\Enum\SubscribeStatus;

/**
 * @extends AbstractCrudController<Record>
 */
#[AdminCrud(routePath: '/subscription/record', routeName: 'subscription_record')]
final class RecordCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Record::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('订阅记录')
            ->setEntityLabelInPlural('订阅记录管理')
            ->setPageTitle('index', '订阅记录列表')
            ->setPageTitle('new', '新建订阅记录')
            ->setPageTitle('edit', '编辑订阅记录')
            ->setPageTitle('detail', '订阅记录详情')
            ->setHelp('index', '管理用户订阅记录，包括订阅状态、时间和关联信息')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['user.username', 'plan.name'])
            ->setPaginatorPageSize(50)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->setMaxLength(9999)
            ->hideOnForm()
        ;

        yield BooleanField::new('valid', '有效状态')
            ->setHelp('标记该订阅记录是否有效')
            ->setFormTypeOption('attr', ['checked' => 'checked'])
        ;

        yield AssociationField::new('plan', '订阅套餐')
            ->setRequired(true)
            ->setHelp('用户订阅的套餐计划')
            ->hideOnIndex()
        ;

        // 在列表页显示简化的plan信息
        if (Crud::PAGE_INDEX === $pageName) {
            yield AssociationField::new('plan', '订阅套餐')
                ->setColumns(3)
            ;
        }

        yield DateTimeField::new('activeTime', '激活时间')
            ->setRequired(true)
            ->setFormat('yyyy-MM-dd HH:mm:ss')
            ->setHelp('订阅激活的时间')
            ->setColumns(6)
        ;

        yield DateTimeField::new('expireTime', '过期时间')
            ->setRequired(true)
            ->setFormat('yyyy-MM-dd HH:mm:ss')
            ->setHelp('订阅过期的时间')
            ->setColumns(6)
        ;

        yield AssociationField::new('user', '用户')
            ->setRequired(true)
            ->setHelp('订阅用户')
            ->hideOnIndex()
        ;

        // 在列表页显示简化的user信息
        if (Crud::PAGE_INDEX === $pageName) {
            yield AssociationField::new('user', '用户')
                ->setColumns(2)
            ;
        }

        yield ChoiceField::new('status', '订阅状态')
            ->setChoices([
                '活跃' => SubscribeStatus::ACTIVE,
                '过期' => SubscribeStatus::EXPIRED,
            ])
            ->setRequired(true)
            ->setHelp('订阅的当前状态')
            ->setColumns(3)
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
            ->onlyOnDetail()
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
            ->onlyOnDetail()
        ;
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
            ->add(BooleanFilter::new('valid', '有效状态'))
            ->add(EntityFilter::new('plan', '订阅套餐'))
            ->add(EntityFilter::new('user', '用户'))
            ->add(DateTimeFilter::new('activeTime', '激活时间'))
            ->add(DateTimeFilter::new('expireTime', '过期时间'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
            ->add(DateTimeFilter::new('updateTime', '更新时间'))
        ;
    }
}
