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
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Tourze\SubscriptionBundle\Entity\Resource;

/**
 * @extends AbstractCrudController<Resource>
 */
#[AdminCrud(routePath: '/subscription/resource', routeName: 'subscription_resource')]
final class ResourceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Resource::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('订阅资源')
            ->setEntityLabelInPlural('订阅资源管理')
            ->setPageTitle('index', '订阅资源列表')
            ->setPageTitle('new', '新建订阅资源')
            ->setPageTitle('edit', '编辑订阅资源')
            ->setPageTitle('detail', '订阅资源详情')
            ->setHelp('index', '管理用户的订阅资源使用情况，包括权益分配和剩余数量')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['user.username', 'record.id', 'equity.name', 'value'])
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
            ->setHelp('标记该资源记录是否有效')
            ->setFormTypeOption('attr', ['checked' => 'checked'])
        ;

        $userField = AssociationField::new('user', '用户')
            ->setRequired(true)
            ->setHelp('拥有该资源的用户')
        ;
        if (Crud::PAGE_INDEX === $pageName) {
            $userField->setColumns(2);
        } else {
            // 非index页面显示完整信息
        }
        yield $userField;

        $recordField = AssociationField::new('record', '订阅记录')
            ->setRequired(true)
            ->setHelp('关联的订阅记录')
        ;
        if (Crud::PAGE_INDEX === $pageName) {
            $recordField->setColumns(2);
        } else {
            // 非index页面显示完整信息
        }
        yield $recordField;

        $equityField = AssociationField::new('equity', '权益')
            ->setRequired(true)
            ->setHelp('享有的权益类型')
        ;
        if (Crud::PAGE_INDEX === $pageName) {
            $equityField->setColumns(3);
        } else {
            // 非index页面显示完整信息
        }
        yield $equityField;

        yield DateTimeField::new('startTime', '开始时间')
            ->setRequired(true)
            ->setFormat('yyyy-MM-dd HH:mm:ss')
            ->setHelp('资源开始生效的时间')
            ->setColumns(6)
        ;

        yield DateTimeField::new('endTime', '结束时间')
            ->setFormat('yyyy-MM-dd HH:mm:ss')
            ->setHelp('资源结束生效的时间（可选）')
            ->setColumns(6)
        ;

        yield TextField::new('value', '剩余数量')
            ->setRequired(true)
            ->setMaxLength(20)
            ->setHelp('资源的剩余数量或额度')
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
            ->add(EntityFilter::new('user', '用户'))
            ->add(EntityFilter::new('record', '订阅记录'))
            ->add(EntityFilter::new('equity', '权益'))
            ->add(TextFilter::new('value', '剩余数量'))
            ->add(DateTimeFilter::new('startTime', '开始时间'))
            ->add(DateTimeFilter::new('endTime', '结束时间'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
            ->add(DateTimeFilter::new('updateTime', '更新时间'))
        ;
    }
}
