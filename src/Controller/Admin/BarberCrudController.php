<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Barber;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class BarberCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Barber::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Coiffeur')
            ->setEntityLabelInPlural('Coiffeurs');
    }

    public function updateEntity(
        EntityManagerInterface $entityManager,
                               $entityInstance
    ): void {
        $conn = $entityManager->getConnection();

        die(
            '<h1>TEST TRANSACTION</h1>
            <p>Transaction active : <strong>' .
            ($conn->isTransactionActive() ? 'OUI' : 'NON') .
            '</strong></p>'
        );
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Nom'),

            TextField::new('speciality', 'Spécialité'),

            TextareaField::new('bio', 'Biographie')
                ->hideOnIndex(),

            ImageField::new('photo', 'Photo')
                ->setBasePath('/uploads/barbers')
                ->setUploadDir('public/uploads/barbers')
                ->setUploadedFileNamePattern('[name]-[timestamp].[extension]')
                ->hideOnIndex(),

            BooleanField::new('isActive', 'Actif'),

            DateTimeField::new('createdAt', 'Créé le')
                ->hideOnForm()
                ->setFormat('dd/MM/yyyy'),
        ];
    }
}
