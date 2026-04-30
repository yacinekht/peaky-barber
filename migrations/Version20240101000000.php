<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240101000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create barber, service and appointment tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE barber (
            id INT AUTO_INCREMENT NOT NULL,
            name VARCHAR(255) NOT NULL,
            photo VARCHAR(255) DEFAULT NULL,
            bio LONGTEXT DEFAULT NULL,
            speciality VARCHAR(255) DEFAULT NULL,
            is_active TINYINT(1) NOT NULL,
            created_at DATETIME NOT NULL,
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE service (
            id INT AUTO_INCREMENT NOT NULL,
            name VARCHAR(255) NOT NULL,
            description LONGTEXT DEFAULT NULL,
            price INT NOT NULL,
            duration INT NOT NULL,
            is_active TINYINT(1) NOT NULL,
            created_at DATETIME NOT NULL,
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE appointment (
            id INT AUTO_INCREMENT NOT NULL,
            service_id INT NOT NULL,
            barber_id INT NOT NULL,
            appointment_at DATETIME NOT NULL,
            client_name VARCHAR(255) NOT NULL,
            client_email VARCHAR(255) NOT NULL,
            client_phone VARCHAR(20) DEFAULT NULL,
            status VARCHAR(50) NOT NULL,
            created_at DATETIME NOT NULL,
            INDEX IDX_appointment_service (service_id),
            INDEX IDX_appointment_barber (barber_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('ALTER TABLE appointment
            ADD CONSTRAINT FK_appointment_service FOREIGN KEY (service_id) REFERENCES service (id),
            ADD CONSTRAINT FK_appointment_barber  FOREIGN KEY (barber_id)  REFERENCES barber (id)
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY FK_appointment_service');
        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY FK_appointment_barber');
        $this->addSql('DROP TABLE appointment');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE barber');
    }
}
