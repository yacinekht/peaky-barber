<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Service;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

class ServiceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Service::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Service')
            ->setEntityLabelInPlural('Services & Prix');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Nom du service'),
            TextareaField::new('description', 'Description')->hideOnIndex(),
            IntegerField::new('price', 'Prix (centimes)')
                ->setHelp('Entrez le prix en centimes. Ex: 2500 = 25,00 €'),
            IntegerField::new('duration', 'Durée (minutes)'),
            BooleanField::new('isActive', 'Actif'),
            DateTimeField::new('createdAt', 'Créé le')
                ->hideOnForm()
                ->setFormat('dd/MM/yyyy'),
        ];
    }
}
