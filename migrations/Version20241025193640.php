<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241025193640 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE genre DROP FOREIGN KEY FK_835033F82C8080DD');
        $this->addSql('DROP INDEX IDX_835033F82C8080DD ON genre');
        $this->addSql('ALTER TABLE genre DROP book_review_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE genre ADD book_review_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE genre ADD CONSTRAINT FK_835033F82C8080DD FOREIGN KEY (book_review_id) REFERENCES book_review (id)');
        $this->addSql('CREATE INDEX IDX_835033F82C8080DD ON genre (book_review_id)');
    }
}
