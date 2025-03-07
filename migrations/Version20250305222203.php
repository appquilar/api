<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250305222203 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE companies (id BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, owner_id BINARY(16) NOT NULL, fiscal_identifier VARCHAR(255) DEFAULT NULL, address LONGTEXT DEFAULT NULL, postal_code VARCHAR(20) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, contact_email VARCHAR(255) DEFAULT NULL, country_code VARCHAR(5) NOT NULL, prefix VARCHAR(5) NOT NULL, number VARCHAR(20) NOT NULL, UNIQUE INDEX unique_owner_id (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE users ADD wordpress_id INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE companies');
        $this->addSql('ALTER TABLE users DROP wordpress_id');
    }
}
