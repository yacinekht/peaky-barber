<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Appointment;
use App\Entity\Barber;
use App\Entity\Service;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AppointmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('service', EntityType::class, [
                'class'        => Service::class,
                'choice_label' => function (Service $s) {
                    return $s->getName() . ' — ' . $s->getPriceFormatted() . ' (' . $s->getDuration() . ' min)';
                },
                'placeholder' => 'Choisissez un service',
                'label'       => 'Service',
                'query_builder' => function ($repo) {
                    return $repo->createQueryBuilder('s')->where('s.isActive = true');
                },
            ])
            ->add('barber', EntityType::class, [
                'class'        => Barber::class,
                'choice_label' => 'name',
                'placeholder'  => 'Choisissez un coiffeur',
                'label'        => 'Coiffeur',
                'query_builder' => function ($repo) {
                    return $repo->createQueryBuilder('b')->where('b.isActive = true');
                },
            ])
            ->add('appointmentAt', DateTimeType::class, [
                'widget' => 'single_text',
                'html5'  => true,
                'label'  => 'Date et heure',
                'input'  => 'datetime',
                'attr'   => [
                    'min' => (new \DateTime('+1 hour'))->format('Y-m-d\TH:i'),
                ],
            ])
            ->add('clientName', TextType::class, [
                'label' => 'Votre nom',
                'attr'  => ['placeholder' => 'Jean Dupont'],
            ])
            ->add('clientEmail', EmailType::class, [
                'label' => 'Votre email',
                'attr'  => ['placeholder' => 'jean@exemple.fr'],
            ])
            ->add('clientPhone', TelType::class, [
                'label'    => 'Votre téléphone',
                'required' => false,
                'attr'     => ['placeholder' => '06 00 00 00 00'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Appointment::class,
        ]);
    }
}