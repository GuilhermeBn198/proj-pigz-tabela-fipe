<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250417123710 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE vehicle DROP FOREIGN KEY FK_1B80E486BCD423EC
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_1B80E486BCD423EC ON vehicle
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE vehicle CHANGE status status ENUM('for_sale', 'sold') NOT NULL DEFAULT 'for_sale', CHANGE year_entity_id year_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE vehicle ADD CONSTRAINT FK_1B80E48640C1FEA7 FOREIGN KEY (year_id) REFERENCES year (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_1B80E48640C1FEA7 ON vehicle (year_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE vehicle DROP FOREIGN KEY FK_1B80E48640C1FEA7
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_1B80E48640C1FEA7 ON vehicle
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE vehicle CHANGE status status ENUM('for_sale', 'sold') DEFAULT 'for_sale' NOT NULL, CHANGE year_id year_entity_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE vehicle ADD CONSTRAINT FK_1B80E486BCD423EC FOREIGN KEY (year_entity_id) REFERENCES year (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_1B80E486BCD423EC ON vehicle (year_entity_id)
        SQL);
    }
}
