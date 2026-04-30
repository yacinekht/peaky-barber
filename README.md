# 🪒 Peaky Barber — Application Symfony de réservation

## Prérequis

- PHP 8.2+
- Composer
- MySQL 8+ (ou MariaDB 10.5+)
- Symfony CLI (recommandé) ou serveur PHP

---

## Installation

### 1. Installer les dépendances

```bash
composer install
```

### 2. Configurer la base de données

Copiez `.env` en `.env.local` et adaptez `DATABASE_URL` :

```bash
cp .env .env.local
```

Éditez `.env.local` :
```
DATABASE_URL="mysql://VOTRE_USER:VOTRE_MOT_DE_PASSE@127.0.0.1:3306/peaky_barber?serverVersion=8.0.32&charset=utf8mb4"
```

### 3. Créer la base de données et exécuter les migrations

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 4. (Optionnel) Charger les données de démonstration

```bash
composer require --dev doctrine/doctrine-fixtures-bundle
php bin/console doctrine:fixtures:load
```

Cela crée : 6 services, 3 coiffeurs, 5 rendez-vous d'exemple.

### 5. Créer le dossier d'upload des photos

```bash
mkdir -p public/uploads/barbers
```

### 6. Lancer le serveur de développement

```bash
symfony serve
# ou
php -S localhost:8000 -t public/
```

---

## Configuration Email

Pour les emails de confirmation, configurez `MAILER_DSN` dans `.env.local` :

```bash
# Mailpit (dev local — https://mailpit.axllent.org)
MAILER_DSN=smtp://localhost:1025

# Mailtrap (test)
MAILER_DSN=smtp://user:pass@sandbox.smtp.mailtrap.io:2525

# Brevo / Sendinblue (production)
MAILER_DSN=smtp://user:password@smtp-relay.brevo.com:587?encryption=tls

# Désactiver les emails (dev)
MAILER_DSN=null://null
```

---

## Structure du projet

```
src/
├── Controller/
│   ├── BookingController.php       # Home, formulaire réservation, API créneaux
│   └── Admin/
│       ├── DashboardController.php # EasyAdmin dashboard
│       ├── AppointmentCrudController.php
│       ├── BarberCrudController.php
│       └── ServiceCrudController.php
├── Entity/
│   ├── Appointment.php
│   ├── Barber.php
│   └── Service.php
├── Form/
│   └── AppointmentType.php
├── Repository/
│   ├── AppointmentRepository.php
│   ├── BarberRepository.php
│   └── ServiceRepository.php
└── DataFixtures/
    └── AppFixtures.php

templates/
├── base.html.twig
├── booking/
│   ├── home.html.twig      # Page d'accueil
│   ├── new.html.twig       # Formulaire de réservation
│   └── success.html.twig   # Page de confirmation
├── email/
│   └── confirmation.html.twig
└── admin/
    └── dashboard.html.twig

migrations/
└── Version20240101000000.php
```

---

## Routes

| Route                          | URL                              | Description                    |
|-------------------------------|----------------------------------|--------------------------------|
| `home`                         | `/`                              | Page d'accueil                 |
| `booking_new`                  | `/reservation`                   | Formulaire de réservation      |
| `booking_success`              | `/reservation/confirmation/{id}` | Page de succès                 |
| `api_slots`                    | `/api/slots?barber=X&date=Y`    | API JSON des créneaux dispo    |
| `admin`                        | `/admin`                         | Dashboard EasyAdmin            |

---

## Sécuriser l'admin en production

Dans `config/packages/security.yaml`, décommentez :

```yaml
access_control:
    - { path: ^/admin, roles: ROLE_ADMIN }
```

Puis créez un utilisateur admin avec `php bin/console make:user` et `make:auth`.

---

## Fonctionnalités

- ✅ Page d'accueil avec services et équipe
- ✅ Formulaire de réservation en 3 étapes
- ✅ Sélecteur de créneaux dynamique (JavaScript + API)
- ✅ Email de confirmation HTML au client
- ✅ Page de confirmation récapitulative
- ✅ Dashboard admin EasyAdmin avec stats
- ✅ CRUD Réservations / Coiffeurs / Services
- ✅ Filtres et badges de statut en admin
- ✅ Fixtures de démo
