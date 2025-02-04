<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250204130844 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE user_token_id_seq CASCADE');
        $this->addSql('ALTER TABLE tentative DROP CONSTRAINT fkh7w61j20a77gqyr4dbq29i3k4');
        $this->addSql('ALTER TABLE token DROP CONSTRAINT fkfv3ur6bq9yddjsc1m6c91uelt');
        $this->addSql('ALTER TABLE blocked DROP CONSTRAINT fkhsnmvustgvu90ggeijbl5la5h');
        $this->addSql('DROP TABLE setting');
        $this->addSql('DROP TABLE user_token');
        $this->addSql('DROP TABLE tentative');
        $this->addSql('DROP TABLE token');
        $this->addSql('DROP TABLE blocked');
        $this->addSql('DROP TABLE users');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE user_token_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE setting (id_setting BIGINT NOT NULL, daty TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, session_duree DOUBLE PRECISION NOT NULL, tentative_max DOUBLE PRECISION NOT NULL, PRIMARY KEY(id_setting))');
        $this->addSql('CREATE TABLE user_token (id SERIAL NOT NULL, id_user INT NOT NULL, token VARCHAR(255) NOT NULL, temps_expiration TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE tentative (id_tentative BIGINT NOT NULL, id_user BIGINT NOT NULL, compteur DOUBLE PRECISION NOT NULL, daty TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id_tentative))');
        $this->addSql('CREATE INDEX IDX_DBC382F96B3CA4B ON tentative (id_user)');
        $this->addSql('CREATE TABLE token (id_token BIGINT NOT NULL, id_user BIGINT NOT NULL, active BOOLEAN NOT NULL, expiration TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, pin VARCHAR(255) DEFAULT NULL, token VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id_token))');
        $this->addSql('CREATE INDEX IDX_5F37A13B6B3CA4B ON token (id_user)');
        $this->addSql('CREATE TABLE blocked (id_blocked BIGINT NOT NULL, id_user BIGINT NOT NULL, expiration TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id_blocked))');
        $this->addSql('CREATE INDEX IDX_DA55EB806B3CA4B ON blocked (id_user)');
        $this->addSql('CREATE TABLE users (id_user BIGINT NOT NULL, email VARCHAR(255) NOT NULL, is_verified BOOLEAN NOT NULL, nom VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, prenom VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id_user))');
        $this->addSql('CREATE UNIQUE INDEX uk6dotkott2kjsp8vw4d0m25fb7 ON users (email)');
        $this->addSql('ALTER TABLE tentative ADD CONSTRAINT fkh7w61j20a77gqyr4dbq29i3k4 FOREIGN KEY (id_user) REFERENCES users (id_user) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE token ADD CONSTRAINT fkfv3ur6bq9yddjsc1m6c91uelt FOREIGN KEY (id_user) REFERENCES users (id_user) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE blocked ADD CONSTRAINT fkhsnmvustgvu90ggeijbl5la5h FOREIGN KEY (id_user) REFERENCES users (id_user) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
