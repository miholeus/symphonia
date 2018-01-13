<?php

namespace Soulex\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180113220530 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_role (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, title VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_status (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) DEFAULT NULL, INDEX user_statuses_code__idx (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE users ADD status_id INT NOT NULL, ADD role_id INT NOT NULL, ADD firstname VARCHAR(255) NOT NULL, ADD lastname VARCHAR(255) NOT NULL, ADD middlename VARCHAR(255) DEFAULT NULL, ADD login VARCHAR(255) NOT NULL, ADD birth_date DATE DEFAULT NULL, ADD avatar VARCHAR(255) DEFAULT NULL, ADD avatar_small VARCHAR(255) DEFAULT NULL, ADD phone VARCHAR(255) NOT NULL, ADD last_login_on DATETIME DEFAULT NULL, ADD created_on DATETIME DEFAULT NULL, ADD updated_on DATETIME DEFAULT NULL, ADD mail_notification TINYINT(1) DEFAULT NULL, ADD must_change_passwd TINYINT(1) DEFAULT NULL, ADD passwd_changed_on DATETIME DEFAULT NULL, ADD is_active TINYINT(1) DEFAULT NULL, ADD is_blocked TINYINT(1) DEFAULT \'0\' NOT NULL, ADD is_deleted TINYINT(1) DEFAULT \'0\' NOT NULL, ADD verify_email_uuid VARCHAR(50) DEFAULT NULL, ADD is_superuser TINYINT(1) DEFAULT NULL, DROP username, DROP first_name, DROP last_name, DROP registerDate, DROP lastvisitDate, DROP role, CHANGE email email VARCHAR(255) DEFAULT NULL, CHANGE password password VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E96BF700BD FOREIGN KEY (status_id) REFERENCES user_status (id) ON DELETE RESTRICT');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9D60322AC FOREIGN KEY (role_id) REFERENCES user_role (id) ON DELETE RESTRICT');
        $this->addSql('CREATE INDEX IDX_1483A5E96BF700BD ON users (status_id)');
        $this->addSql('CREATE INDEX IDX_1483A5E9D60322AC ON users (role_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9D60322AC');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E96BF700BD');
        $this->addSql('DROP TABLE user_role');
        $this->addSql('DROP TABLE user_status');
        $this->addSql('DROP INDEX IDX_1483A5E96BF700BD ON users');
        $this->addSql('DROP INDEX IDX_1483A5E9D60322AC ON users');
        $this->addSql('ALTER TABLE users ADD username VARCHAR(100) DEFAULT NULL COLLATE utf8_unicode_ci, ADD last_name VARCHAR(50) DEFAULT NULL COLLATE utf8_unicode_ci, ADD registerDate DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, ADD lastvisitDate DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, ADD role VARCHAR(25) DEFAULT NULL COLLATE utf8_unicode_ci, DROP status_id, DROP role_id, DROP firstname, DROP lastname, DROP middlename, DROP login, DROP birth_date, DROP avatar, DROP avatar_small, DROP phone, DROP last_login_on, DROP created_on, DROP updated_on, DROP mail_notification, DROP must_change_passwd, DROP passwd_changed_on, DROP is_active, DROP is_blocked, DROP is_deleted, DROP is_superuser, CHANGE email email VARCHAR(100) NOT NULL COLLATE utf8_unicode_ci, CHANGE password password VARCHAR(32) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE verify_email_uuid first_name VARCHAR(50) DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}
