<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241025201937 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book_review ADD reviewer_id INT NOT NULL, DROP reviewer');
        $this->addSql('ALTER TABLE book_review ADD CONSTRAINT FK_50948A4B70574616 FOREIGN KEY (reviewer_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_50948A4B70574616 ON book_review (reviewer_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book_review DROP FOREIGN KEY FK_50948A4B70574616');
        $this->addSql('DROP INDEX IDX_50948A4B70574616 ON book_review');
        $this->addSql('ALTER TABLE book_review ADD reviewer VARCHAR(255) NOT NULL, DROP reviewer_id');
    }
}
