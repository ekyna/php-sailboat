<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201230101144 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE boat (id INT AUTO_INCREMENT NOT NULL, position_id INT DEFAULT NULL, name VARCHAR(32) NOT NULL, UNIQUE INDEX UNIQ_D86E834ADD842E46 (position_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE position (id INT AUTO_INCREMENT NOT NULL, boat_id INT NOT NULL, date DATETIME NOT NULL, longitude INT NOT NULL, latitude INT NOT NULL, direction SMALLINT NOT NULL, speed SMALLINT NOT NULL, INDEX IDX_462CE4F5A1E84A29 (boat_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE boat ADD CONSTRAINT FK_D86E834ADD842E46 FOREIGN KEY (position_id) REFERENCES position (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE position ADD CONSTRAINT FK_462CE4F5A1E84A29 FOREIGN KEY (boat_id) REFERENCES boat (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE position DROP FOREIGN KEY FK_462CE4F5A1E84A29');
        $this->addSql('ALTER TABLE boat DROP FOREIGN KEY FK_D86E834ADD842E46');
        $this->addSql('DROP TABLE boat');
        $this->addSql('DROP TABLE position');
    }
}
