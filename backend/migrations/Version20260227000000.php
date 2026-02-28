<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260227000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create initial schema for TechCompare';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE users (
            id INT AUTO_INCREMENT NOT NULL, 
            email VARCHAR(180) NOT NULL, 
            roles JSON NOT NULL, 
            password VARCHAR(255) NOT NULL, 
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', 
            UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), 
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        
        $this->addSql('CREATE TABLE comparisons (
            id INT AUTO_INCREMENT NOT NULL, 
            user_id INT DEFAULT NULL, 
            winner_product_id INT DEFAULT NULL, 
            status VARCHAR(50) NOT NULL, 
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', 
            INDEX IDX_339C81A9A76ED395 (user_id), 
            INDEX IDX_339C81A9B7930B90 (winner_product_id), 
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        
        $this->addSql('CREATE TABLE products (
            id INT AUTO_INCREMENT NOT NULL, 
            comparison_id INT NOT NULL, 
            name VARCHAR(255) NOT NULL, 
            brand VARCHAR(100) DEFAULT NULL, 
            price NUMERIC(10, 2) DEFAULT NULL, 
            currency VARCHAR(10) DEFAULT NULL, 
            specs JSON DEFAULT NULL, 
            strengths JSON DEFAULT NULL, 
            weaknesses JSON DEFAULT NULL, 
            score NUMERIC(5, 2) DEFAULT NULL, 
            raw_extraction JSON DEFAULT NULL, 
            url VARCHAR(500) DEFAULT NULL, 
            category LONGTEXT DEFAULT NULL, 
            INDEX IDX_B3BA5A5A8807C85 (comparison_id), 
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        
        $this->addSql('CREATE TABLE messenger_messages (
            id BIGINT AUTO_INCREMENT NOT NULL, 
            body LONGTEXT NOT NULL, 
            headers LONGTEXT NOT NULL, 
            queue_name VARCHAR(190) NOT NULL, 
            created_at DATETIME NOT NULL, 
            available_at DATETIME NOT NULL, 
            delivered_at DATETIME DEFAULT NULL, 
            INDEX IDX_75EA56E0FB7336F0 (queue_name), 
            INDEX IDX_75EA56E0E3BD61CE (available_at), 
            INDEX IDX_75EA56E016BA31DB (delivered_at), 
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        
        $this->addSql('ALTER TABLE comparisons ADD CONSTRAINT FK_339C81A9A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE comparisons ADD CONSTRAINT FK_339C81A9B7930B90 FOREIGN KEY (winner_product_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5A8807C85 FOREIGN KEY (comparison_id) REFERENCES comparisons (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comparisons DROP FOREIGN KEY FK_339C81A9A76ED395');
        $this->addSql('ALTER TABLE comparisons DROP FOREIGN KEY FK_339C81A9B7930B90');
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5A8807C85');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE comparisons');
        $this->addSql('DROP TABLE products');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
