<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230319075221 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE admin_acc (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP INDEX user_id ON profile');
        $this->addSql('ALTER TABLE profile DROP user_id');
        $this->addSql('DROP INDEX id ON user');
        $this->addSql('DROP INDEX id_2 ON user');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE admin_acc');
        $this->addSql('ALTER TABLE profile ADD user_id INT NOT NULL');
        $this->addSql('CREATE INDEX user_id ON profile (user_id)');
        $this->addSql('CREATE INDEX id ON user (id)');
        $this->addSql('CREATE INDEX id_2 ON user (id)');
    }
}
