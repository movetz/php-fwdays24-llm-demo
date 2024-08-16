<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240806105820 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE EXTENSION vector');
        $this->addSql('CREATE TABLE speaker (id INT NOT NULL, name VARCHAR(255) NOT NULL, position VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE talk (id INT NOT NULL, speaker_id INT DEFAULT NULL, name VARCHAR(512) NOT NULL, description VARCHAR(2048) NOT NULL, embedding vector(1536) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9F24D5BBD04A0F27 ON talk (speaker_id)');
        $this->addSql('ALTER TABLE talk ADD CONSTRAINT FK_9F24D5BBD04A0F27 FOREIGN KEY (speaker_id) REFERENCES speaker (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX ON talk USING hnsw (embedding vector_cosine_ops)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE talk DROP CONSTRAINT FK_9F24D5BBD04A0F27');
        $this->addSql('DROP TABLE speaker');
        $this->addSql('DROP TABLE talk');
    }
}
