<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201207115357 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contest ALTER applications_deadline TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE contest ALTER applications_deadline DROP DEFAULT');
        $this->addSql('ALTER TABLE contest ALTER vote_start_time TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE contest ALTER vote_start_time DROP DEFAULT');
        $this->addSql('ALTER TABLE contest ALTER vote_end_time TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE contest ALTER vote_end_time DROP DEFAULT');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE contest ALTER applications_deadline TYPE DATE');
        $this->addSql('ALTER TABLE contest ALTER applications_deadline DROP DEFAULT');
        $this->addSql('ALTER TABLE contest ALTER vote_start_time TYPE DATE');
        $this->addSql('ALTER TABLE contest ALTER vote_start_time DROP DEFAULT');
        $this->addSql('ALTER TABLE contest ALTER vote_end_time TYPE DATE');
        $this->addSql('ALTER TABLE contest ALTER vote_end_time DROP DEFAULT');
    }
}
