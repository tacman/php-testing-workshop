<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250319180937 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add database schema.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE agenda (id CHAR(36) NOT NULL, is_published TINYINT(1) NOT NULL, owner_id CHAR(36) NOT NULL, UNIQUE INDEX UNIQ_2CEDC8777E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE agenda_slot (id CHAR(36) NOT NULL, opening_at DATETIME NOT NULL, closing_at DATETIME NOT NULL, status VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, agenda_id CHAR(36) NOT NULL, INDEX IDX_912B8217EA67784A (agenda_id), UNIQUE INDEX agenda_slot_window_unique (agenda_id, opening_at, closing_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE health_specialist (id CHAR(36) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, specialty VARCHAR(255) NOT NULL, introduction LONGTEXT DEFAULT NULL, biography LONGTEXT DEFAULT NULL, profile_picture_url LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE medical_appointment (id CHAR(36) NOT NULL, opening_at DATETIME NOT NULL, closing_at DATETIME NOT NULL, reference_number VARCHAR(30) NOT NULL, first_name VARCHAR(255) NOT NULL, folded_first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, folded_last_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, cancelled_at DATETIME NOT NULL, cancellation_reason LONGTEXT NOT NULL, practitioner_id CHAR(36) NOT NULL, INDEX IDX_6CC137F61121EA2C (practitioner_id), UNIQUE INDEX medical_appointment_reference_number_unique (reference_number), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE agenda ADD CONSTRAINT FK_2CEDC8777E3C61F9 FOREIGN KEY (owner_id) REFERENCES health_specialist (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE agenda_slot ADD CONSTRAINT FK_912B8217EA67784A FOREIGN KEY (agenda_id) REFERENCES agenda (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE medical_appointment ADD CONSTRAINT FK_6CC137F61121EA2C FOREIGN KEY (practitioner_id) REFERENCES health_specialist (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE agenda DROP FOREIGN KEY FK_2CEDC8777E3C61F9');
        $this->addSql('ALTER TABLE agenda_slot DROP FOREIGN KEY FK_912B8217EA67784A');
        $this->addSql('ALTER TABLE medical_appointment DROP FOREIGN KEY FK_6CC137F61121EA2C');
        $this->addSql('DROP TABLE agenda');
        $this->addSql('DROP TABLE agenda_slot');
        $this->addSql('DROP TABLE health_specialist');
        $this->addSql('DROP TABLE medical_appointment');
    }
}
