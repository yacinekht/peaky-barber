<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Service;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

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

    public function updateEntity(
        EntityManagerInterface $entityManager,
                               $entityInstance
    ): void {
        try {
            parent::updateEntity($entityManager, $entityInstance);
        } catch (\Throwable $e) {
            die(
                '<pre>' .
                get_class($e) . "\n\n" .
                $e->getMessage() . "\n\n" .
                $e->getTraceAsString() .
                '</pre>'
            );
        }
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Nom du service'),

            TextareaField::new('description', 'Description')
                ->hideOnIndex(),

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