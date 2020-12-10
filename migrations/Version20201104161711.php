symfony <?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201104161711 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE contest_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE contestants_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE organizer_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE photo_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE vote_log_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE contest (id INT NOT NULL, user_limit INT NOT NULL, photo_limit INT NOT NULL, applications_deadline DATE NOT NULL, vote_start_time DATE NOT NULL, vote_end_time DATE NOT NULL, theme VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE contestants (id INT NOT NULL, user_id_id INT NOT NULL, contest_id INT NOT NULL, photo_count INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_25006B6E9D86650F ON contestants (user_id_id)');
        $this->addSql('CREATE INDEX IDX_25006B6E1CD0F0DE ON contestants (contest_id)');
        $this->addSql('CREATE TABLE organizer (id INT NOT NULL, contest_id INT NOT NULL, user_id_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_99D471731CD0F0DE ON organizer (contest_id)');
        $this->addSql('CREATE INDEX IDX_99D471739D86650F ON organizer (user_id_id)');
        $this->addSql('CREATE TABLE photo (id INT NOT NULL, author_id INT NOT NULL, filepath VARCHAR(255) NOT NULL, score INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_14B78418F675F31B ON photo (author_id)');
        $this->addSql('CREATE TABLE photo_contest (photo_id INT NOT NULL, contest_id INT NOT NULL, PRIMARY KEY(photo_id, contest_id))');
        $this->addSql('CREATE INDEX IDX_34494B387E9E4C8C ON photo_contest (photo_id)');
        $this->addSql('CREATE INDEX IDX_34494B381CD0F0DE ON photo_contest (contest_id)');
        $this->addSql('CREATE TABLE vote_log (id INT NOT NULL, date DATE NOT NULL, grade INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE vote_log_user_accounts (vote_log_id INT NOT NULL, user_accounts_id INT NOT NULL, PRIMARY KEY(vote_log_id, user_accounts_id))');
        $this->addSql('CREATE INDEX IDX_747D048BE6802FDC ON vote_log_user_accounts (vote_log_id)');
        $this->addSql('CREATE INDEX IDX_747D048B6545457D ON vote_log_user_accounts (user_accounts_id)');
        $this->addSql('CREATE TABLE vote_log_photo (vote_log_id INT NOT NULL, photo_id INT NOT NULL, PRIMARY KEY(vote_log_id, photo_id))');
        $this->addSql('CREATE INDEX IDX_7685A880E6802FDC ON vote_log_photo (vote_log_id)');
        $this->addSql('CREATE INDEX IDX_7685A8807E9E4C8C ON vote_log_photo (photo_id)');
        $this->addSql('ALTER TABLE contestants ADD CONSTRAINT FK_25006B6E9D86650F FOREIGN KEY (user_id_id) REFERENCES user_accounts (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE contestants ADD CONSTRAINT FK_25006B6E1CD0F0DE FOREIGN KEY (contest_id) REFERENCES contest (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE organizer ADD CONSTRAINT FK_99D471731CD0F0DE FOREIGN KEY (contest_id) REFERENCES contest (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE organizer ADD CONSTRAINT FK_99D471739D86650F FOREIGN KEY (user_id_id) REFERENCES user_accounts (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B78418F675F31B FOREIGN KEY (author_id) REFERENCES user_accounts (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE photo_contest ADD CONSTRAINT FK_34494B387E9E4C8C FOREIGN KEY (photo_id) REFERENCES photo (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE photo_contest ADD CONSTRAINT FK_34494B381CD0F0DE FOREIGN KEY (contest_id) REFERENCES contest (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE vote_log_user_accounts ADD CONSTRAINT FK_747D048BE6802FDC FOREIGN KEY (vote_log_id) REFERENCES vote_log (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE vote_log_user_accounts ADD CONSTRAINT FK_747D048B6545457D FOREIGN KEY (user_accounts_id) REFERENCES user_accounts (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE vote_log_photo ADD CONSTRAINT FK_7685A880E6802FDC FOREIGN KEY (vote_log_id) REFERENCES vote_log (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE vote_log_photo ADD CONSTRAINT FK_7685A8807E9E4C8C FOREIGN KEY (photo_id) REFERENCES photo (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE contestants DROP CONSTRAINT FK_25006B6E1CD0F0DE');
        $this->addSql('ALTER TABLE organizer DROP CONSTRAINT FK_99D471731CD0F0DE');
        $this->addSql('ALTER TABLE photo_contest DROP CONSTRAINT FK_34494B381CD0F0DE');
        $this->addSql('ALTER TABLE photo_contest DROP CONSTRAINT FK_34494B387E9E4C8C');
        $this->addSql('ALTER TABLE vote_log_photo DROP CONSTRAINT FK_7685A8807E9E4C8C');
        $this->addSql('ALTER TABLE vote_log_user_accounts DROP CONSTRAINT FK_747D048BE6802FDC');
        $this->addSql('ALTER TABLE vote_log_photo DROP CONSTRAINT FK_7685A880E6802FDC');
        $this->addSql('DROP SEQUENCE contest_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE contestants_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE organizer_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE photo_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE vote_log_id_seq CASCADE');
        $this->addSql('DROP TABLE contest');
        $this->addSql('DROP TABLE contestants');
        $this->addSql('DROP TABLE organizer');
        $this->addSql('DROP TABLE photo');
        $this->addSql('DROP TABLE photo_contest');
        $this->addSql('DROP TABLE vote_log');
        $this->addSql('DROP TABLE vote_log_user_accounts');
        $this->addSql('DROP TABLE vote_log_photo');
    }
}
