<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250415213925 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE brand ADD type VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE vehicle ADD sale_price NUMERIC(10, 2) DEFAULT NULL, ADD year_entity_id INT NOT NULL, DROP year, CHANGE status status ENUM('for_sale', 'sold') NOT NULL DEFAULT 'for_sale'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE vehicle ADD CONSTRAINT FK_1B80E486BCD423EC FOREIGN KEY (year_entity_id) REFERENCES year (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_1B80E486BCD423EC ON vehicle (year_entity_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE brand DROP type
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE vehicle DROP FOREIGN KEY FK_1B80E486BCD423EC
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_1B80E486BCD423EC ON vehicle
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE vehicle ADD year SMALLINT NOT NULL, DROP sale_price, DROP year_entity_id, CHANGE status status ENUM('for_sale', 'sold') DEFAULT 'for_sale' NOT NULL
        SQL);
    }
}
