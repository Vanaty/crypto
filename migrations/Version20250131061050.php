<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250131061050 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE crypto_transaction ADD crypto_cours_id INT NOT NULL');
        $this->addSql('ALTER TABLE crypto_transaction ADD CONSTRAINT FK_5380A1D54EABFBFF FOREIGN KEY (crypto_cours_id) REFERENCES crypto_cours (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_5380A1D54EABFBFF ON crypto_transaction (crypto_cours_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE crypto_transaction DROP CONSTRAINT FK_5380A1D54EABFBFF');
        $this->addSql('DROP INDEX IDX_5380A1D54EABFBFF');
        $this->addSql('ALTER TABLE crypto_transaction DROP crypto_cours_id');
    }
}
