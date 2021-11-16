<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211008060513 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE Account_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE comment_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE customer_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE Account (id INT NOT NULL, account_holder_id INT NOT NULL, account_manager_id INT NOT NULL, balance DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B28B6F38FC94BA8B ON Account (account_holder_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B28B6F3884A5C6C7 ON Account (account_manager_id)');
        $this->addSql('CREATE TABLE comment (id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, email VARCHAR(150) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE customer (id INT NOT NULL, name VARCHAR(155) DEFAULT NULL, email VARCHAR(150) DEFAULT NULL, phone VARCHAR(50) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(150) DEFAULT NULL, username VARCHAR(150) DEFAULT NULL, phone VARCHAR(100) DEFAULT NULL, admin_only_property VARCHAR(150) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('ALTER TABLE Account ADD CONSTRAINT FK_B28B6F38FC94BA8B FOREIGN KEY (account_holder_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE Account ADD CONSTRAINT FK_B28B6F3884A5C6C7 FOREIGN KEY (account_manager_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE Account DROP CONSTRAINT FK_B28B6F38FC94BA8B');
        $this->addSql('ALTER TABLE Account DROP CONSTRAINT FK_B28B6F3884A5C6C7');
        $this->addSql('DROP SEQUENCE Account_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE comment_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE customer_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('DROP TABLE Account');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE customer');
        $this->addSql('DROP TABLE "user"');
    }
}
