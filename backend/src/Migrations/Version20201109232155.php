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


        $this->addSql('CREATE EXTENSION IF NOT EXISTS "pgcrypto";');
        /*Create a trigger function that takes no arguments.
            Trigger functions automatically have OLD, NEW records
            and TG_TABLE_NAME as well as others.*/
        $this->addSql(" CREATE OR REPLACE FUNCTION unique_short_guid()
            RETURNS TRIGGER AS $$
            
             -- Declare the variables we'll be using.
            DECLARE
              key TEXT;
              qry TEXT;
              found TEXT;
            BEGIN
            
              -- generate the first part of a query as a string with safely
              -- escaped table name, using || to concat the parts
              qry := 'SELECT guid FROM ' || quote_ident(TG_TABLE_NAME) || ' WHERE guid=';
            
              -- This loop will probably only run once per call until we've generated
              -- millions of ids.
              LOOP
            
                -- Generate our string bytes and re-encode as a base64 string.
                key := encode(gen_random_bytes(6), 'base64');
            
                -- Base64 encoding contains 2 URL unsafe characters by default.
                -- The URL-safe version has these replacements.
                key := replace(key, '/', '_'); -- url safe replacement
                key := replace(key, '+', '-'); -- url safe replacement
            
                -- Concat the generated key (safely quoted) with the generated query
                -- and run it.
                -- SELECT guid FROM test WHERE guid='blahblah' INTO found
                -- Now found will be the duplicated guid or NULL.
                EXECUTE qry || quote_literal(key) INTO found;
            
                -- Check to see if found is NULL.
                -- If we checked to see if found = NULL it would always be FALSE
                -- because (NULL = NULL) is always FALSE.
                IF found IS NULL THEN
            
                  -- If we didn't find a collision then leave the LOOP.
                  EXIT;
                END IF;
            
                -- We haven't EXITed yet, so return to the top of the LOOP
                -- and try again.
              END LOOP;
            
              -- NEW and OLD are available in TRIGGER PROCEDURES.
              -- NEW is the mutated row that will actually be INSERTed.
              -- We're replacing guid, regardless of what it was before
              -- with our key variable.
              NEW.guid = key;
            
              -- The RECORD returned here is what will actually be INSERTed,
              -- or what the next trigger will get if there is one.
              RETURN NEW;
            END;
            $$ language 'plpgsql';");

        $this->addSql('CREATE TRIGGER trigger_guid_generation BEFORE INSERT ON link FOR EACH ROW EXECUTE PROCEDURE unique_short_guid();');

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
