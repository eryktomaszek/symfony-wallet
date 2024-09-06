<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240906135728 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, slug VARCHAR(255) NOT NULL, UNIQUE INDEX uq_categories_title (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tags (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(64) NOT NULL, slug VARCHAR(64) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_6FBC94262B36786B (title), UNIQUE INDEX UNIQ_6FBC9426989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transaction (id INT AUTO_INCREMENT NOT NULL, wallet_id INT NOT NULL, category_id INT NOT NULL, author_id INT NOT NULL, amount DOUBLE PRECISION NOT NULL, description VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, date DATETIME NOT NULL, balance_after DOUBLE PRECISION DEFAULT NULL, INDEX IDX_723705D1712520F3 (wallet_id), INDEX IDX_723705D112469DE2 (category_id), INDEX IDX_723705D1F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transaction_tags (transaction_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_8DFFAA872FC0CB0F (transaction_id), INDEX IDX_8DFFAA87BAD26311 (tag_id), PRIMARY KEY(transaction_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX email_idx (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wallets (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, balance DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, slug VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1712520F3 FOREIGN KEY (wallet_id) REFERENCES wallets (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D112469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1F675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE transaction_tags ADD CONSTRAINT FK_8DFFAA872FC0CB0F FOREIGN KEY (transaction_id) REFERENCES transaction (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE transaction_tags ADD CONSTRAINT FK_8DFFAA87BAD26311 FOREIGN KEY (tag_id) REFERENCES tags (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1712520F3');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D112469DE2');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1F675F31B');
        $this->addSql('ALTER TABLE transaction_tags DROP FOREIGN KEY FK_8DFFAA872FC0CB0F');
        $this->addSql('ALTER TABLE transaction_tags DROP FOREIGN KEY FK_8DFFAA87BAD26311');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE tags');
        $this->addSql('DROP TABLE transaction');
        $this->addSql('DROP TABLE transaction_tags');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE wallets');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
