<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250130182222 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE chat_group (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, visibility INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE messages ADD chatgroup_id INT NOT NULL');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E96C612F453 FOREIGN KEY (chatgroup_id) REFERENCES chat_group (id)');
        $this->addSql('CREATE INDEX IDX_DB021E96C612F453 ON messages (chatgroup_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY FK_DB021E96C612F453');
        $this->addSql('DROP TABLE chat_group');
        $this->addSql('DROP INDEX IDX_DB021E96C612F453 ON messages');
        $this->addSql('ALTER TABLE messages DROP chatgroup_id');
    }
}
