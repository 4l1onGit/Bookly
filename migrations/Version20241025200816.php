<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241025200816 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE genre DROP FOREIGN KEY FK_835033F816A2B381');
        $this->addSql('DROP INDEX IDX_835033F816A2B381 ON genre');
        $this->addSql('ALTER TABLE genre DROP book_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE genre ADD book_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE genre ADD CONSTRAINT FK_835033F816A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('CREATE INDEX IDX_835033F816A2B381 ON genre (book_id)');
    }
}
