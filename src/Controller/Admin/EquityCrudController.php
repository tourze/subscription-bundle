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
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Tourze\SubscriptionBundle\Entity\Equity;

/**
 * @extends AbstractCrudController<Equity>
 */
#[AdminCrud(routePath: '/subscription/equity', routeName: 'subscription_equity')]
final class EquityCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Equity::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('权益')
            ->setEntityLabelInPlural('权益管理')
            ->setPageTitle('index', '权益列表')
            ->setPageTitle('new', '新建权益')
            ->setPageTitle('edit', '编辑权益')
            ->setPageTitle('detail', '权益详情')
            ->setHelp('index', '管理订阅权益配置，包括权益类型、数值、描述等')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['name', 'type', 'description'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->setMaxLength(9999)
            ->hideOnForm()
        ;

        yield TextField::new('name', '权益名称')
            ->setMaxLength(120)
            ->setRequired(true)
            ->setHelp('权益的名称，最多120个字符')
        ;

        yield TextField::new('type', '权益类型')
            ->setMaxLength(20)
            ->setRequired(true)
            ->setHelp('权益的类型，如：traffic(流量)、lottery(抽奖次数)、storage(存储空间)等')
        ;

        yield TextField::new('value', '权益数值')
            ->setRequired(true)
            ->setHelp('权益的具体数值，如流量大小、次数等')
        ;

        yield TextareaField::new('description', '权益描述')
            ->setNumOfRows(4)
            ->setHelp('详细描述权益的内容和使用说明')
            ->hideOnIndex()
        ;

        yield AssociationField::new('plans', '关联计划')
            ->setHelp('包含该权益的订阅计划')
            ->hideOnForm()
            ->onlyOnDetail()
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
            ->hideOnIndex()
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
            ->hideOnIndex()
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
            ->add(TextFilter::new('name', '权益名称'))
            ->add(TextFilter::new('type', '权益类型'))
            ->add(TextFilter::new('value', '权益数值'))
            ->add(EntityFilter::new('plans', '关联计划'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
            ->add(DateTimeFilter::new('updateTime', '更新时间'))
        ;
    }
}
