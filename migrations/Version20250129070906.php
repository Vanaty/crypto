<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250129070906 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE config_devise (id SERIAL NOT NULL, devise_id INT NOT NULL, devise_base_id INT NOT NULL, valeur NUMERIC(18, 8) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_358CC81DF4445056 ON config_devise (devise_id)');
        $this->addSql('CREATE INDEX IDX_358CC81DC47F8734 ON config_devise (devise_base_id)');
        $this->addSql('CREATE TABLE config_token (id SERIAL NOT NULL, duree_de_vie_token INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE crypto (id SERIAL NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE crypto_cours (id SERIAL NOT NULL, devise_id INT NOT NULL, crypto_id INT NOT NULL, cours NUMERIC(18, 8) NOT NULL, datetime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_EB7C9CE7F4445056 ON crypto_cours (devise_id)');
        $this->addSql('CREATE INDEX IDX_EB7C9CE7E9571A63 ON crypto_cours (crypto_id)');
        $this->addSql('CREATE TABLE crypto_transaction (id SERIAL NOT NULL, crypto_id INT NOT NULL, devise_id INT NOT NULL, id_user INT NOT NULL, entre NUMERIC(18, 8) NOT NULL, sortie NUMERIC(18, 8) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5380A1D5E9571A63 ON crypto_transaction (crypto_id)');
        $this->addSql('CREATE INDEX IDX_5380A1D5F4445056 ON crypto_transaction (devise_id)');
        $this->addSql('CREATE TABLE devise (id SERIAL NOT NULL, valeur NUMERIC(18, 8) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE user_token (id SERIAL NOT NULL, id_user INT NOT NULL, token VARCHAR(255) NOT NULL, temps_expiration TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE user_transaction (id SERIAL NOT NULL, devise_id INT NOT NULL, id_user INT NOT NULL, entre NUMERIC(18, 8) NOT NULL, sortie NUMERIC(18, 8) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DB2CCC44F4445056 ON user_transaction (devise_id)');
        $this->addSql('ALTER TABLE config_devise ADD CONSTRAINT FK_358CC81DF4445056 FOREIGN KEY (devise_id) REFERENCES devise (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE config_devise ADD CONSTRAINT FK_358CC81DC47F8734 FOREIGN KEY (devise_base_id) REFERENCES devise (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE crypto_cours ADD CONSTRAINT FK_EB7C9CE7F4445056 FOREIGN KEY (devise_id) REFERENCES devise (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE crypto_cours ADD CONSTRAINT FK_EB7C9CE7E9571A63 FOREIGN KEY (crypto_id) REFERENCES crypto (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE crypto_transaction ADD CONSTRAINT FK_5380A1D5E9571A63 FOREIGN KEY (crypto_id) REFERENCES crypto (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE crypto_transaction ADD CONSTRAINT FK_5380A1D5F4445056 FOREIGN KEY (devise_id) REFERENCES devise (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_transaction ADD CONSTRAINT FK_DB2CCC44F4445056 FOREIGN KEY (devise_id) REFERENCES devise (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE config_devise DROP CONSTRAINT FK_358CC81DF4445056');
        $this->addSql('ALTER TABLE config_devise DROP CONSTRAINT FK_358CC81DC47F8734');
        $this->addSql('ALTER TABLE crypto_cours DROP CONSTRAINT FK_EB7C9CE7F4445056');
        $this->addSql('ALTER TABLE crypto_cours DROP CONSTRAINT FK_EB7C9CE7E9571A63');
        $this->addSql('ALTER TABLE crypto_transaction DROP CONSTRAINT FK_5380A1D5E9571A63');
        $this->addSql('ALTER TABLE crypto_transaction DROP CONSTRAINT FK_5380A1D5F4445056');
        $this->addSql('ALTER TABLE user_transaction DROP CONSTRAINT FK_DB2CCC44F4445056');
        $this->addSql('DROP TABLE config_devise');
        $this->addSql('DROP TABLE config_token');
        $this->addSql('DROP TABLE crypto');
        $this->addSql('DROP TABLE crypto_cours');
        $this->addSql('DROP TABLE crypto_transaction');
        $this->addSql('DROP TABLE devise');
        $this->addSql('DROP TABLE user_token');
        $this->addSql('DROP TABLE user_transaction');
    }
}
