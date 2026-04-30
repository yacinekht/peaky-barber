<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Appointment;
use App\Entity\Barber;
use App\Entity\Service;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // ── Services ──────────────────────────────────────────────
        $services = [
            ['Coupe Classique',      'Coupe ciseau ou tondeuse, finitions soignées.',           2000, 30],
            ['Coupe + Barbe',        'Coupe complète avec taille et mise en forme de la barbe.',3500, 60],
            ['Rasage Traditionnel',  'Rasage au coupe-chou avec serviette chaude.',             2500, 45],
            ['Dégradé Américain',    'Fondu parfait, rendu net et précis.',                     2200, 35],
            ['Coupe Enfant',         'Pour les moins de 12 ans.',                               1500, 25],
            ['Barbe Seule',          'Taille, contour et soin de la barbe.',                    1500, 30],
        ];

        $serviceEntities = [];
        foreach ($services as [$name, $desc, $price, $duration]) {
            $s = new Service();
            $s->setName($name)->setDescription($desc)->setPrice($price)->setDuration($duration);
            $manager->persist($s);
            $serviceEntities[] = $s;
        }

        // ── Barbers ───────────────────────────────────────────────
        $barbers = [
            ['Arthur Shelby', 'Maître barbier depuis 12 ans. Spécialiste du rasage traditionnel et des coupes vintage.',  'Rasage Traditionnel'],
            ['Tommy Blade',   'Expert en dégradés et coiffures modernes. Formé à Londres et Milan.',                       'Dégradé & Styling'],
            ['Alfie Carter',  'Artisan passionné, connu pour son sens du détail et ses finitions impeccables.',            'Coupe Classique'],
        ];

        $barberEntities = [];
        foreach ($barbers as [$name, $bio, $speciality]) {
            $b = new Barber();
            $b->setName($name)->setBio($bio)->setSpeciality($speciality);
            $manager->persist($b);
            $barberEntities[] = $b;
        }

        $manager->flush();

        // ── Appointments ──────────────────────────────────────────
        $now = new \DateTime();
        $sampleAppointments = [
            ['+1 day 10:00',  'Jean Dupont',    'jean@exemple.fr',   '0601020304', 'confirmed'],
            ['+1 day 11:00',  'Marie Martin',   'marie@exemple.fr',  null,          'confirmed'],
            ['+2 days 14:00', 'Paul Bernard',   'paul@exemple.fr',   '0611223344', 'pending'],
            ['+2 days 15:30', 'Sophie Leroy',   'sophie@exemple.fr', null,          'pending'],
            ['-1 day 09:30',  'Lucas Morel',    'lucas@exemple.fr',  '0622334455', 'confirmed'],
        ];

        foreach ($sampleAppointments as $i => [$when, $name, $email, $phone, $status]) {
            $dt = (clone $now)->modify($when);
            $a = new Appointment();
            $a->setService($serviceEntities[$i % count($serviceEntities)])
              ->setBarber($barberEntities[$i % count($barberEntities)])
              ->setAppointmentAt($dt)
              ->setClientName($name)
              ->setClientEmail($email)
              ->setClientPhone($phone)
              ->setStatus($status);
            $manager->persist($a);
        }

        $manager->flush();
    }
}
