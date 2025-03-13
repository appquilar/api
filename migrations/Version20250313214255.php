<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250313214255 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE access_tokens (id BINARY(16) NOT NULL, token VARCHAR(300) NOT NULL, user_id BINARY(16) NOT NULL, site_id BINARY(16) NOT NULL, revoked TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_58D184BC5F37A13B (token), INDEX token_idx (token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE companies (id BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, fiscal_identifier VARCHAR(255) DEFAULT NULL, address LONGTEXT DEFAULT NULL, postal_code VARCHAR(20) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, contact_email VARCHAR(255) DEFAULT NULL, country_code VARCHAR(5) DEFAULT NULL, prefix VARCHAR(5) DEFAULT NULL, number VARCHAR(20) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE company_users (id BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, company_id BINARY(16) NOT NULL, user_id BINARY(16) DEFAULT NULL, email VARCHAR(255) NOT NULL, role VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, invitation_expires_at TIME NOT NULL, invitation_token VARCHAR(40) NOT NULL, INDEX token_idx (invitation_token), INDEX company_user_idx (company_id, user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE forgot_password_tokens (id BINARY(16) NOT NULL, user_id BINARY(16) NOT NULL, site_id BINARY(16) NOT NULL, token VARCHAR(300) NOT NULL, expires_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_2350282B5F37A13B (token), INDEX token_idx (token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE media (id BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, original_filename VARCHAR(255) NOT NULL, mime_type VARCHAR(255) NOT NULL, size INT NOT NULL, width INT DEFAULT NULL, height INT DEFAULT NULL, INDEX media_filename_idx (original_filename), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE users (id BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, wordpress_password VARCHAR(255) DEFAULT NULL, roles JSON NOT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, wordpress_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), INDEX email_idx (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE access_tokens');
        $this->addSql('DROP TABLE companies');
        $this->addSql('DROP TABLE company_users');
        $this->addSql('DROP TABLE forgot_password_tokens');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP TABLE users');
    }
}
