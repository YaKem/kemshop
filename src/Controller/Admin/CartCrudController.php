<?php

namespace App\Controller\Admin;

use App\Entity\Cart;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CartCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Cart::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('user.FullName'),
            TextField::new('carrierName'),
            MoneyField::new('carrierPrice')->setCurrency('EUR'),
            MoneyField::new('subTotalHT')->setCurrency('EUR'),
            MoneyField::new('taxe')->setCurrency('EUR'),
            MoneyField::new('subTotalTTC')->setCurrency('EUR'),
            BooleanField::new('isPaid')
        ];
    }
}
