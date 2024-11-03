<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241025193337 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE book (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book_review (id INT AUTO_INCREMENT NOT NULL, book_id INT NOT NULL, title VARCHAR(255) NOT NULL, author VARCHAR(255) NOT NULL, pages INT DEFAULT NULL, summary VARCHAR(255) NOT NULL, reviewer VARCHAR(255) NOT NULL, review_text VARCHAR(255) NOT NULL, INDEX IDX_50948A4B16A2B381 (book_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE genre (id INT AUTO_INCREMENT NOT NULL, book_review_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_835033F82C8080DD (book_review_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, role_id INT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, INDEX IDX_8D93D649D60322AC (role_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_book_review (user_id INT NOT NULL, book_review_id INT NOT NULL, INDEX IDX_F98F43DEA76ED395 (user_id), INDEX IDX_F98F43DE2C8080DD (book_review_id), PRIMARY KEY(user_id, book_review_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE book_review ADD CONSTRAINT FK_50948A4B16A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE genre ADD CONSTRAINT FK_835033F82C8080DD FOREIGN KEY (book_review_id) REFERENCES book_review (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649D60322AC FOREIGN KEY (role_id) REFERENCES role (id)');
        $this->addSql('ALTER TABLE user_book_review ADD CONSTRAINT FK_F98F43DEA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_book_review ADD CONSTRAINT FK_F98F43DE2C8080DD FOREIGN KEY (book_review_id) REFERENCES book_review (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book_review DROP FOREIGN KEY FK_50948A4B16A2B381');
        $this->addSql('ALTER TABLE genre DROP FOREIGN KEY FK_835033F82C8080DD');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649D60322AC');
        $this->addSql('ALTER TABLE user_book_review DROP FOREIGN KEY FK_F98F43DEA76ED395');
        $this->addSql('ALTER TABLE user_book_review DROP FOREIGN KEY FK_F98F43DE2C8080DD');
        $this->addSql('DROP TABLE book');
        $this->addSql('DROP TABLE book_review');
        $this->addSql('DROP TABLE genre');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE user_book_review');
    }
}
