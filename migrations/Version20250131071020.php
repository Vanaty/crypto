<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250131071020 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE crypto_transaction DROP CONSTRAINT fk_5380a1d54eabfbff');
        $this->addSql('DROP INDEX idx_5380a1d54eabfbff');
        $this->addSql('ALTER TABLE crypto_transaction ADD crypto_cours NUMERIC(18, 8) NOT NULL');
        $this->addSql('ALTER TABLE crypto_transaction DROP crypto_cours_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE crypto_transaction ADD crypto_cours_id INT NOT NULL');
        $this->addSql('ALTER TABLE crypto_transaction DROP crypto_cours');
        $this->addSql('ALTER TABLE crypto_transaction ADD CONSTRAINT fk_5380a1d54eabfbff FOREIGN KEY (crypto_cours_id) REFERENCES crypto_cours (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_5380a1d54eabfbff ON crypto_transaction (crypto_cours_id)');
    }
}
