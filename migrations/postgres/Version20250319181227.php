<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250319181227 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add database schema.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE agenda (id UUID NOT NULL, is_published BOOLEAN NOT NULL, owner_id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2CEDC8777E3C61F9 ON agenda (owner_id)');
        $this->addSql('CREATE TABLE agenda_slot (id UUID NOT NULL, opening_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, closing_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, status VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, agenda_id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_912B8217EA67784A ON agenda_slot (agenda_id)');
        $this->addSql('CREATE UNIQUE INDEX agenda_slot_window_unique ON agenda_slot (agenda_id, opening_at, closing_at)');
        $this->addSql('CREATE TABLE health_specialist (id UUID NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, specialty VARCHAR(255) NOT NULL, introduction TEXT DEFAULT NULL, biography TEXT DEFAULT NULL, profile_picture_url TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE medical_appointment (id UUID NOT NULL, opening_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, closing_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, reference_number VARCHAR(30) NOT NULL, first_name VARCHAR(255) NOT NULL, folded_first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, folded_last_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, cancelled_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, cancellation_reason TEXT NOT NULL, practitioner_id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6CC137F61121EA2C ON medical_appointment (practitioner_id)');
        $this->addSql('CREATE UNIQUE INDEX medical_appointment_reference_number_unique ON medical_appointment (reference_number)');
        $this->addSql('ALTER TABLE agenda ADD CONSTRAINT FK_2CEDC8777E3C61F9 FOREIGN KEY (owner_id) REFERENCES health_specialist (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE agenda_slot ADD CONSTRAINT FK_912B8217EA67784A FOREIGN KEY (agenda_id) REFERENCES agenda (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE medical_appointment ADD CONSTRAINT FK_6CC137F61121EA2C FOREIGN KEY (practitioner_id) REFERENCES health_specialist (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE agenda DROP CONSTRAINT FK_2CEDC8777E3C61F9');
        $this->addSql('ALTER TABLE agenda_slot DROP CONSTRAINT FK_912B8217EA67784A');
        $this->addSql('ALTER TABLE medical_appointment DROP CONSTRAINT FK_6CC137F61121EA2C');
        $this->addSql('DROP TABLE agenda');
        $this->addSql('DROP TABLE agenda_slot');
        $this->addSql('DROP TABLE health_specialist');
        $this->addSql('DROP TABLE medical_appointment');
    }
}
