<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250417132931 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE vehicle ADD requested_by_id INT DEFAULT NULL, CHANGE status status ENUM('for_sale', 'pending', 'sold') NOT NULL DEFAULT 'for_sale'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE vehicle ADD CONSTRAINT FK_1B80E4864DA1E751 FOREIGN KEY (requested_by_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_1B80E4864DA1E751 ON vehicle (requested_by_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE vehicle DROP FOREIGN KEY FK_1B80E4864DA1E751
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_1B80E4864DA1E751 ON vehicle
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE vehicle DROP requested_by_id, CHANGE status status ENUM('for_sale', 'sold') DEFAULT 'for_sale' NOT NULL
        SQL);
    }
}
