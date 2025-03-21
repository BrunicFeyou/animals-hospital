<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250321110330 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE animal (id INT AUTO_INCREMENT NOT NULL, picture_id INT DEFAULT NULL, owner_id INT NOT NULL, name VARCHAR(255) NOT NULL, race VARCHAR(255) NOT NULL, birth_date DATETIME NOT NULL, UNIQUE INDEX UNIQ_6AAB231FEE45BDBF (picture_id), INDEX IDX_6AAB231F7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE appointment (id INT AUTO_INCREMENT NOT NULL, animal_id INT NOT NULL, veterinary_id INT NOT NULL, assistant_id INT DEFAULT NULL, created_date DATETIME NOT NULL, appointment_date DATETIME NOT NULL, reason LONGTEXT NOT NULL, state VARCHAR(255) NOT NULL, INDEX IDX_FE38F8448E962C16 (animal_id), INDEX IDX_FE38F844D954EB99 (veterinary_id), INDEX IDX_FE38F844E05387EF (assistant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE appointment_treatment (appointment_id INT NOT NULL, treatment_id INT NOT NULL, INDEX IDX_D8B5238E5B533F9 (appointment_id), INDEX IDX_D8B5238471C0366 (treatment_id), PRIMARY KEY(appointment_id, treatment_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media (id INT AUTO_INCREMENT NOT NULL, file_path VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE treatment (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, price DOUBLE PRECISION NOT NULL, duration VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, surname VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE animal ADD CONSTRAINT FK_6AAB231FEE45BDBF FOREIGN KEY (picture_id) REFERENCES media (id)');
        $this->addSql('ALTER TABLE animal ADD CONSTRAINT FK_6AAB231F7E3C61F9 FOREIGN KEY (owner_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F8448E962C16 FOREIGN KEY (animal_id) REFERENCES animal (id)');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F844D954EB99 FOREIGN KEY (veterinary_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F844E05387EF FOREIGN KEY (assistant_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE appointment_treatment ADD CONSTRAINT FK_D8B5238E5B533F9 FOREIGN KEY (appointment_id) REFERENCES appointment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE appointment_treatment ADD CONSTRAINT FK_D8B5238471C0366 FOREIGN KEY (treatment_id) REFERENCES treatment (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE animal DROP FOREIGN KEY FK_6AAB231FEE45BDBF');
        $this->addSql('ALTER TABLE animal DROP FOREIGN KEY FK_6AAB231F7E3C61F9');
        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY FK_FE38F8448E962C16');
        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY FK_FE38F844D954EB99');
        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY FK_FE38F844E05387EF');
        $this->addSql('ALTER TABLE appointment_treatment DROP FOREIGN KEY FK_D8B5238E5B533F9');
        $this->addSql('ALTER TABLE appointment_treatment DROP FOREIGN KEY FK_D8B5238471C0366');
        $this->addSql('DROP TABLE animal');
        $this->addSql('DROP TABLE appointment');
        $this->addSql('DROP TABLE appointment_treatment');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP TABLE treatment');
        $this->addSql('DROP TABLE `user`');
    }
}
