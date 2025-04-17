<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250415215702 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE vehicle CHANGE status status ENUM('for_sale', 'sold') NOT NULL DEFAULT 'for_sale'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE year ADD model_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE year ADD CONSTRAINT FK_BB8273377975B7E7 FOREIGN KEY (model_id) REFERENCES model (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_BB8273377975B7E7 ON year (model_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE year DROP FOREIGN KEY FK_BB8273377975B7E7
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_BB8273377975B7E7 ON year
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE year DROP model_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE vehicle CHANGE status status ENUM('for_sale', 'sold') DEFAULT 'for_sale' NOT NULL
        SQL);
    }
}
