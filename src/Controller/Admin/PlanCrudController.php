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
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Tourze\SubscriptionBundle\Entity\Plan;

/**
 * @extends AbstractCrudController<Plan>
 */
#[AdminCrud(routePath: '/subscription/plan', routeName: 'subscription_plan')]
final class PlanCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Plan::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('订阅计划')
            ->setEntityLabelInPlural('订阅计划管理')
            ->setPageTitle('index', '订阅计划列表')
            ->setPageTitle('new', '新建订阅计划')
            ->setPageTitle('edit', '编辑订阅计划')
            ->setPageTitle('detail', '订阅计划详情')
            ->setHelp('index', '管理订阅计划配置，包括计划名称、有效期、权益等')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['name', 'description'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->setMaxLength(9999)
            ->hideOnForm()
        ;

        yield BooleanField::new('valid', '有效状态')
            ->setHelp('是否启用该订阅计划')
            ->setFormTypeOption('attr', ['checked' => 'checked'])
        ;

        yield TextField::new('name', '计划名称')
            ->setMaxLength(64)
            ->setRequired(true)
            ->setHelp('订阅计划的名称，最多64个字符')
        ;

        yield TextareaField::new('description', '计划描述')
            ->setNumOfRows(4)
            ->setHelp('详细描述订阅计划的内容和特色')
            ->hideOnIndex()
        ;

        yield IntegerField::new('periodDay', '生效天数')
            ->setRequired(true)
            ->setHelp('订阅计划的有效期天数，如30表示30天有效期')
        ;

        yield IntegerField::new('renewCount', '可续订次数')
            ->setRequired(true)
            ->setHelp('用户可以续订的次数，0表示无限制')
        ;

        yield AssociationField::new('equities', '包含权益')
            ->setHelp('该订阅计划包含的权益列表')
            ->hideOnIndex()
        ;

        yield AssociationField::new('records', '订阅记录')
            ->setHelp('使用该计划的订阅记录')
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
            ->add(TextFilter::new('name', '计划名称'))
            ->add(BooleanFilter::new('valid', '有效状态'))
            ->add(NumericFilter::new('periodDay', '生效天数'))
            ->add(NumericFilter::new('renewCount', '可续订次数'))
            ->add(EntityFilter::new('equities', '权益'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
            ->add(DateTimeFilter::new('updateTime', '更新时间'))
        ;
    }
}
