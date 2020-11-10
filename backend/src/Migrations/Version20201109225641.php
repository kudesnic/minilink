<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201109225641 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE link ADD owner_id INT NOT NULL');
        $this->addSql('ALTER TABLE link ADD created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE link ADD CONSTRAINT FK_36AC99F17E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX owner_id_idx ON link (owner_id)');
        $this->addSql('ALTER TABLE users DROP img');
        $this->addSql('ALTER TABLE users ALTER roles TYPE json');
        $this->addSql('ALTER TABLE users ALTER roles DROP DEFAULT');

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE link DROP CONSTRAINT FK_36AC99F17E3C61F9');
        $this->addSql('DROP INDEX owner_id_idx');
        $this->addSql('ALTER TABLE link DROP owner_id');
        $this->addSql('ALTER TABLE link DROP created');
        $this->addSql('ALTER TABLE users ADD img VARCHAR(255) DEFAULT \'resources/default-user-image.png\' NOT NULL');
        $this->addSql('ALTER TABLE users ALTER roles TYPE JSON');
        $this->addSql('ALTER TABLE users ALTER roles DROP DEFAULT');
    }
}
