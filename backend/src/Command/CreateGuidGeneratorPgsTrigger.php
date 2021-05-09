<?php
namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateGuidGeneratorPgsTrigger extends Command
{
    protected static $defaultName = 'app:create-guid-generator-pgs-trigger';
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;

        parent::__construct();
    }

    protected function configure()
    {
        // ...
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $conn = $this->em->getConnection();
            $stmt = $conn->prepare('CREATE EXTENSION IF NOT EXISTS "pgcrypto"');
            $stmt->execute();

            $stmt = $conn->prepare("CREATE OR REPLACE FUNCTION unique_short_guid()
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
            $stmt->execute();

            $conn->prepare("CREATE TRIGGER trigger_guid_generation BEFORE INSERT ON link FOR EACH ROW EXECUTE PROCEDURE unique_short_guid();");
            $stmt->execute();
            $output->writeln("GUUID generator trigger created!");
        } catch (\Error $e){
            throw new \ErrorException($e->getMessage());
        }

        return 0;
    }
}
