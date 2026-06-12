<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260612112026 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ticket ADD date_resolution TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE ticket ADD date_fermeture TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE ticket ALTER statut TYPE VARCHAR(50)');
        $this->addSql('ALTER TABLE ticket ALTER priorite TYPE VARCHAR(50)');
        $this->addSql('ALTER TABLE ticket ALTER traitee_par_id DROP NOT NULL');
        $this->addSql('ALTER TABLE ticket ALTER panne_id DROP NOT NULL');
        $this->addSql('ALTER TABLE ticket ALTER sla_id DROP NOT NULL');
        $this->addSql('ALTER TABLE ticket ALTER equipement_id DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ticket DROP date_resolution');
        $this->addSql('ALTER TABLE ticket DROP date_fermeture');
        $this->addSql('ALTER TABLE ticket ALTER statut TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE ticket ALTER priorite TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE ticket ALTER traitee_par_id SET NOT NULL');
        $this->addSql('ALTER TABLE ticket ALTER panne_id SET NOT NULL');
        $this->addSql('ALTER TABLE ticket ALTER sla_id SET NOT NULL');
        $this->addSql('ALTER TABLE ticket ALTER equipement_id SET NOT NULL');
    }
}
