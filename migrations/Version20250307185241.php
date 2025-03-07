<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250307185241 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE company_users (id BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, company_id BINARY(16) NOT NULL, user_id BINARY(16) NOT NULL, company_user_role VARCHAR(255) NOT NULL, UNIQUE INDEX company_user_idx (company_id, user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('DROP INDEX unique_owner_id ON companies');
        $this->addSql('ALTER TABLE companies DROP owner_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE company_users');
        $this->addSql('ALTER TABLE companies ADD owner_id BINARY(16) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX unique_owner_id ON companies (owner_id)');
    }
}
