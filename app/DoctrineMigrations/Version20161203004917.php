<?php

namespace Soulex\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161203004917 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE content_nodes (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, value TEXT NOT NULL, isInvokable TINYINT(1) NOT NULL, params VARCHAR(255) DEFAULT NULL, page_id INT UNSIGNED NOT NULL, INDEX page_id (page_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE controllers (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, controller VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE controller_actions (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, action VARCHAR(50) NOT NULL, params VARCHAR(255) DEFAULT NULL, controller_id INT NOT NULL, INDEX controller_id (controller_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE events (id INT UNSIGNED AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, short_description VARCHAR(255) DEFAULT NULL, detail_description MEDIUMTEXT NOT NULL, img_preview VARCHAR(255) NOT NULL, published TINYINT(1) DEFAULT \'0\' NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, published_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE menus (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, menutype VARCHAR(50) NOT NULL, description VARCHAR(255) NOT NULL, UNIQUE INDEX menutype (menutype), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE menu_items (id INT AUTO_INCREMENT NOT NULL, menu_id INT NOT NULL, `label` VARCHAR(255) NOT NULL, uri VARCHAR(255) NOT NULL, position INT NOT NULL, published TINYINT(1) NOT NULL, lft INT NOT NULL COMMENT \'Nested sets left key\', rgt INT NOT NULL COMMENT \'Nested sets right key\', parent_id INT NOT NULL COMMENT \'Adjacency List\', level TINYINT(1) DEFAULT \'1\' NOT NULL, INDEX menu_id (menu_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE news (id INT UNSIGNED AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, short_description VARCHAR(255) DEFAULT NULL, detail_description MEDIUMTEXT NOT NULL, published TINYINT(1) DEFAULT \'0\' NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, published_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pages (id INT UNSIGNED AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, uri VARCHAR(255) NOT NULL, meta_keywords VARCHAR(5000) NOT NULL, meta_description VARCHAR(5000) NOT NULL, published TINYINT(1) DEFAULT \'0\' NOT NULL, lft INT NOT NULL, rgt INT NOT NULL, level TINYINT(1) DEFAULT \'1\' NOT NULL, UNIQUE INDEX lft (lft, rgt), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(100) DEFAULT NULL, email VARCHAR(100) NOT NULL, password VARCHAR(32) DEFAULT NULL, first_name VARCHAR(50) DEFAULT NULL, last_name VARCHAR(50) DEFAULT NULL, registerDate DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, lastvisitDate DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, role VARCHAR(25) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE content_nodes');
        $this->addSql('DROP TABLE controllers');
        $this->addSql('DROP TABLE controller_actions');
        $this->addSql('DROP TABLE events');
        $this->addSql('DROP TABLE menus');
        $this->addSql('DROP TABLE menu_items');
        $this->addSql('DROP TABLE news');
        $this->addSql('DROP TABLE pages');
        $this->addSql('DROP TABLE users');
    }
}
