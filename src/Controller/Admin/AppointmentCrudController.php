<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Appointment;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;

class AppointmentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Appointment::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Réservation')
            ->setEntityLabelInPlural('Réservations')
            ->setDefaultSort(['appointmentAt' => 'DESC']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(ChoiceFilter::new('status')->setChoices([
                'En attente' => 'pending',
                'Confirmé'   => 'confirmed',
                'Annulé'     => 'cancelled',
            ]))
            ->add(DateTimeFilter::new('appointmentAt'));
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('clientName', 'Client'),
            EmailField::new('clientEmail', 'Email'),
            TextField::new('clientPhone', 'Téléphone')->hideOnIndex(),
            AssociationField::new('service', 'Service'),
            AssociationField::new('barber', 'Coiffeur'),
            DateTimeField::new('appointmentAt', 'Date & Heure')
                ->setFormat('dd/MM/yyyy HH:mm'),
            ChoiceField::new('status', 'Statut')
                ->setChoices([
                    'En attente' => 'pending',
                    'Confirmé'   => 'confirmed',
                    'Annulé'     => 'cancelled',
                ])
                ->renderAsBadges([
                    'pending'   => 'warning',
                    'confirmed' => 'success',
                    'cancelled' => 'danger',
                ]),
            DateTimeField::new('createdAt', 'Créé le')
                ->hideOnForm()
                ->setFormat('dd/MM/yyyy HH:mm'),
        ];
    }
}
