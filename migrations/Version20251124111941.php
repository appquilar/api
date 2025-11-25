<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251124111941 extends AbstractMigration
{
    public function isTransactional(): bool
    {
        return false;
    }

    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE access_tokens (id BINARY(16) NOT NULL, token VARCHAR(300) NOT NULL, user_id BINARY(16) NOT NULL, site_id BINARY(16) NOT NULL, revoked TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_58D184BC5F37A13B (token), INDEX token_idx (token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE categories (id BINARY(16) NOT NULL, created_at DATETIME(6) NOT NULL, updated_at DATETIME(6) DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, slug VARCHAR(255) NOT NULL, parent_id BINARY(16) DEFAULT NULL, icon_id VARCHAR(255) DEFAULT NULL, featured_image_id VARCHAR(255) DEFAULT NULL, landscape_image_id VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_3AF34668989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE companies (id BINARY(16) NOT NULL, created_at DATETIME(6) NOT NULL, updated_at DATETIME(6) DEFAULT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, fiscal_identifier VARCHAR(255) DEFAULT NULL, contact_email VARCHAR(255) DEFAULT NULL, country_code VARCHAR(5) DEFAULT NULL, prefix VARCHAR(5) DEFAULT NULL, number VARCHAR(20) DEFAULT NULL, street LONGTEXT DEFAULT NULL, street2 LONGTEXT DEFAULT NULL, city VARCHAR(50) DEFAULT NULL, postal_code VARCHAR(25) DEFAULT NULL, state VARCHAR(50) DEFAULT NULL, country VARCHAR(50) DEFAULT NULL, latitude NUMERIC(10, 7) DEFAULT NULL, longitude NUMERIC(10, 7) DEFAULT NULL, UNIQUE INDEX UNIQ_8244AA3A989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE company_users (id BINARY(16) NOT NULL, created_at DATETIME(6) NOT NULL, updated_at DATETIME(6) DEFAULT NULL, company_id BINARY(16) NOT NULL, user_id BINARY(16) DEFAULT NULL, email VARCHAR(255) NOT NULL, role VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, invitation_expires_at TIME NOT NULL, invitation_token VARCHAR(40) NOT NULL, INDEX token_idx (invitation_token), INDEX company_user_idx (company_id, user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE forgot_password_tokens (id BINARY(16) NOT NULL, user_id BINARY(16) NOT NULL, site_id BINARY(16) NOT NULL, token VARCHAR(300) NOT NULL, expires_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_2350282B5F37A13B (token), INDEX token_idx (token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE media (id BINARY(16) NOT NULL, created_at DATETIME(6) NOT NULL, updated_at DATETIME(6) DEFAULT NULL, original_filename VARCHAR(255) NOT NULL, mime_type VARCHAR(255) NOT NULL, size INT NOT NULL, width INT DEFAULT NULL, height INT DEFAULT NULL, INDEX media_filename_idx (original_filename), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE product_search (id BINARY(16) NOT NULL, created_at DATETIME(6) NOT NULL, updated_at DATETIME(6) DEFAULT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, latitude DOUBLE PRECISION DEFAULT NULL, longitude DOUBLE PRECISION DEFAULT NULL, circle JSON DEFAULT NULL, categories JSON NOT NULL, publication_status VARCHAR(20) NOT NULL, owner_id BINARY(16) NOT NULL, owner_type VARCHAR(20) NOT NULL, image_ids JSON NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE products (id BINARY(16) NOT NULL, created_at DATETIME(6) NOT NULL, updated_at DATETIME(6) DEFAULT NULL, short_id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, internal_id VARCHAR(50) NOT NULL, description LONGTEXT DEFAULT NULL, quantity INT NOT NULL, company_id BINARY(16) DEFAULT NULL, user_id BINARY(16) DEFAULT NULL, category_id BINARY(16) DEFAULT NULL, image_ids JSON NOT NULL, tiers JSON NOT NULL, publication_status_status VARCHAR(20) NOT NULL, publication_status_published_at DATETIME DEFAULT NULL, deposit_amount INT NOT NULL, deposit_currency VARCHAR(3) NOT NULL, UNIQUE INDEX UNIQ_B3BA5A5AF8496E51 (short_id), UNIQUE INDEX UNIQ_B3BA5A5A989D9B62 (slug), INDEX short_id_idx (short_id), INDEX internal_id_idx (internal_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE rents (id BINARY(16) NOT NULL, created_at DATETIME(6) NOT NULL, updated_at DATETIME(6) DEFAULT NULL, product_id BINARY(16) NOT NULL, owner_id BINARY(16) NOT NULL, owner_type VARCHAR(20) NOT NULL, renter_id BINARY(16) DEFAULT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, status VARCHAR(20) NOT NULL, deposit_amount INT NOT NULL, deposit_currency VARCHAR(3) NOT NULL, price_amount INT NOT NULL, price_currency VARCHAR(3) NOT NULL, deposit_returned_amount INT NOT NULL, deposit_returned_currency VARCHAR(3) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE sites (id BINARY(16) NOT NULL, created_at DATETIME(6) NOT NULL, updated_at DATETIME(6) DEFAULT NULL, name VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, logo_id BINARY(16) NOT NULL, favicon_id BINARY(16) NOT NULL, primary_color VARCHAR(25) NOT NULL, category_ids JSON NOT NULL, menu_category_ids JSON NOT NULL, featured_category_ids JSON NOT NULL, INDEX name_idx (name), INDEX url_idx (url), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE users (id BINARY(16) NOT NULL, created_at DATETIME(6) NOT NULL, updated_at DATETIME(6) DEFAULT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, street LONGTEXT DEFAULT NULL, street2 LONGTEXT DEFAULT NULL, city VARCHAR(50) DEFAULT NULL, postal_code VARCHAR(25) DEFAULT NULL, state VARCHAR(50) DEFAULT NULL, country VARCHAR(50) DEFAULT NULL, latitude NUMERIC(10, 7) DEFAULT NULL, longitude NUMERIC(10, 7) DEFAULT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), INDEX email_idx (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE access_tokens');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE companies');
        $this->addSql('DROP TABLE company_users');
        $this->addSql('DROP TABLE forgot_password_tokens');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP TABLE product_search');
        $this->addSql('DROP TABLE products');
        $this->addSql('DROP TABLE rents');
        $this->addSql('DROP TABLE sites');
        $this->addSql('DROP TABLE users');
    }
}
