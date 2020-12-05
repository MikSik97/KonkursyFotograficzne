<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201104161942 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE vote_log_user_accounts');
        $this->addSql('DROP TABLE vote_log_photo');
        $this->addSql('ALTER TABLE vote_log ADD author_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE vote_log ADD photo_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE vote_log ADD CONSTRAINT FK_509262BBF675F31B FOREIGN KEY (author_id) REFERENCES user_accounts (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE vote_log ADD CONSTRAINT FK_509262BB7E9E4C8C FOREIGN KEY (photo_id) REFERENCES photo (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_509262BBF675F31B ON vote_log (author_id)');
        $this->addSql('CREATE INDEX IDX_509262BB7E9E4C8C ON vote_log (photo_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE vote_log_user_accounts (vote_log_id INT NOT NULL, user_accounts_id INT NOT NULL, PRIMARY KEY(vote_log_id, user_accounts_id))');
        $this->addSql('CREATE INDEX idx_747d048b6545457d ON vote_log_user_accounts (user_accounts_id)');
        $this->addSql('CREATE INDEX idx_747d048be6802fdc ON vote_log_user_accounts (vote_log_id)');
        $this->addSql('CREATE TABLE vote_log_photo (vote_log_id INT NOT NULL, photo_id INT NOT NULL, PRIMARY KEY(vote_log_id, photo_id))');
        $this->addSql('CREATE INDEX idx_7685a880e6802fdc ON vote_log_photo (vote_log_id)');
        $this->addSql('CREATE INDEX idx_7685a8807e9e4c8c ON vote_log_photo (photo_id)');
        $this->addSql('ALTER TABLE vote_log_user_accounts ADD CONSTRAINT fk_747d048be6802fdc FOREIGN KEY (vote_log_id) REFERENCES vote_log (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE vote_log_user_accounts ADD CONSTRAINT fk_747d048b6545457d FOREIGN KEY (user_accounts_id) REFERENCES user_accounts (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE vote_log_photo ADD CONSTRAINT fk_7685a880e6802fdc FOREIGN KEY (vote_log_id) REFERENCES vote_log (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE vote_log_photo ADD CONSTRAINT fk_7685a8807e9e4c8c FOREIGN KEY (photo_id) REFERENCES photo (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE vote_log DROP CONSTRAINT FK_509262BBF675F31B');
        $this->addSql('ALTER TABLE vote_log DROP CONSTRAINT FK_509262BB7E9E4C8C');
        $this->addSql('DROP INDEX IDX_509262BBF675F31B');
        $this->addSql('DROP INDEX IDX_509262BB7E9E4C8C');
        $this->addSql('ALTER TABLE vote_log DROP author_id');
        $this->addSql('ALTER TABLE vote_log DROP photo_id');
    }
}
