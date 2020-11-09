<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201108223727 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE country_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE link_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE platform_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE users_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE visit_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE country (id INT NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE link (id INT NOT NULL, guid VARCHAR(8) NOT NULL, link VARCHAR(255) NOT NULL, living_time INT DEFAULT NULL, status VARCHAR(10) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX guid_idx ON link (guid)');
        $this->addSql('CREATE TABLE platform (id INT NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE refresh_tokens (id INT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9BACE7E1C74F2195 ON refresh_tokens (refresh_token)');
        $this->addSql('CREATE TABLE users (id INT NOT NULL, email VARCHAR(180) NOT NULL, img VARCHAR(255) DEFAULT \'resources/default-user-image.png\' NOT NULL, name VARCHAR(255) NOT NULL, telephone VARCHAR(15) DEFAULT NULL, roles json NOT NULL, password VARCHAR(255) NOT NULL, created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        $this->addSql('CREATE TABLE visit (id INT NOT NULL, link_id INT NOT NULL, country_id INT DEFAULT NULL, platform_id INT DEFAULT NULL, user_agent VARCHAR(255) NOT NULL, referer VARCHAR(255) DEFAULT NULL, ip VARCHAR(15) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX link_idx ON visit (link_id)');
        $this->addSql('CREATE INDEX platform_idx ON visit (platform_id)');
        $this->addSql('CREATE INDEX country_idx ON visit (country_id)');
        $this->addSql('ALTER TABLE visit ADD CONSTRAINT FK_437EE939ADA40271 FOREIGN KEY (link_id) REFERENCES link (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE visit ADD CONSTRAINT FK_437EE939F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE visit ADD CONSTRAINT FK_437EE939FFE6496F FOREIGN KEY (platform_id) REFERENCES platform (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE visit DROP CONSTRAINT FK_437EE939F92F3E70');
        $this->addSql('ALTER TABLE visit DROP CONSTRAINT FK_437EE939ADA40271');
        $this->addSql('ALTER TABLE visit DROP CONSTRAINT FK_437EE939FFE6496F');
        $this->addSql('DROP SEQUENCE country_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE link_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE platform_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE refresh_tokens_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE users_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE visit_id_seq CASCADE');
        $this->addSql('DROP TABLE country');
        $this->addSql('DROP TABLE link');
        $this->addSql('DROP TABLE platform');
        $this->addSql('DROP TABLE refresh_tokens');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE visit');
    }
}
