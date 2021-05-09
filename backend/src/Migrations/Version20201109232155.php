<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201109232155 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE link ADD expiration_time INT NOT NULL');
        $this->addSql('ALTER TABLE link DROP living_time');
        $this->addSql('ALTER TABLE users ALTER roles TYPE json');
        $this->addSql('ALTER TABLE users ALTER roles DROP DEFAULT');

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE link ADD living_time INT DEFAULT NULL');
        $this->addSql('ALTER TABLE link DROP expiration_time');
        $this->addSql('ALTER TABLE users ALTER roles TYPE JSON');
        $this->addSql('ALTER TABLE users ALTER roles DROP DEFAULT');
    }
}
