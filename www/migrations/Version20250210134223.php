<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250210134223 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE equipement (id INT AUTO_INCREMENT NOT NULL, equipement VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE hebergement (id INT AUTO_INCREMENT NOT NULL, type_id_id INT DEFAULT NULL, tarif_id_id INT DEFAULT NULL, capacity INT NOT NULL, surface INT NOT NULL, disponibilite TINYINT(1) NOT NULL, description LONGTEXT NOT NULL, INDEX IDX_4852DD9C714819A0 (type_id_id), INDEX IDX_4852DD9CA67005A7 (tarif_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE hebergement_equipement (hebergement_id INT NOT NULL, equipement_id INT NOT NULL, INDEX IDX_4C1E14923BB0F66 (hebergement_id), INDEX IDX_4C1E149806F0F5C (equipement_id), PRIMARY KEY(hebergement_id, equipement_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, hebergement_id INT DEFAULT NULL, image_path VARCHAR(255) NOT NULL, image_name VARCHAR(255) NOT NULL, image_size INT NOT NULL, INDEX IDX_C53D045F23BB0F66 (hebergement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rental (id INT AUTO_INCREMENT NOT NULL, user_id_id INT DEFAULT NULL, nbr_adults INT NOT NULL, nbr_kids INT NOT NULL, date_start DATETIME NOT NULL, date_end DATETIME NOT NULL, INDEX IDX_1619C27D9D86650F (user_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE saison (id INT AUTO_INCREMENT NOT NULL, saison VARCHAR(25) NOT NULL, date_start DATETIME NOT NULL, date_end DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tarif (id INT AUTO_INCREMENT NOT NULL, saison_id_id INT DEFAULT NULL, hebergement_id_id INT DEFAULT NULL, prix DOUBLE PRECISION NOT NULL, UNIQUE INDEX UNIQ_E7189C9CB7B5AFE (saison_id_id), INDEX IDX_E7189C9F130044A (hebergement_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(40) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, username VARCHAR(50) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE hebergement ADD CONSTRAINT FK_4852DD9C714819A0 FOREIGN KEY (type_id_id) REFERENCES type (id)');
        $this->addSql('ALTER TABLE hebergement ADD CONSTRAINT FK_4852DD9CA67005A7 FOREIGN KEY (tarif_id_id) REFERENCES tarif (id)');
        $this->addSql('ALTER TABLE hebergement_equipement ADD CONSTRAINT FK_4C1E14923BB0F66 FOREIGN KEY (hebergement_id) REFERENCES hebergement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE hebergement_equipement ADD CONSTRAINT FK_4C1E149806F0F5C FOREIGN KEY (equipement_id) REFERENCES equipement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F23BB0F66 FOREIGN KEY (hebergement_id) REFERENCES hebergement (id)');
        $this->addSql('ALTER TABLE rental ADD CONSTRAINT FK_1619C27D9D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE tarif ADD CONSTRAINT FK_E7189C9CB7B5AFE FOREIGN KEY (saison_id_id) REFERENCES saison (id)');
        $this->addSql('ALTER TABLE tarif ADD CONSTRAINT FK_E7189C9F130044A FOREIGN KEY (hebergement_id_id) REFERENCES hebergement (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hebergement DROP FOREIGN KEY FK_4852DD9C714819A0');
        $this->addSql('ALTER TABLE hebergement DROP FOREIGN KEY FK_4852DD9CA67005A7');
        $this->addSql('ALTER TABLE hebergement_equipement DROP FOREIGN KEY FK_4C1E14923BB0F66');
        $this->addSql('ALTER TABLE hebergement_equipement DROP FOREIGN KEY FK_4C1E149806F0F5C');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045F23BB0F66');
        $this->addSql('ALTER TABLE rental DROP FOREIGN KEY FK_1619C27D9D86650F');
        $this->addSql('ALTER TABLE tarif DROP FOREIGN KEY FK_E7189C9CB7B5AFE');
        $this->addSql('ALTER TABLE tarif DROP FOREIGN KEY FK_E7189C9F130044A');
        $this->addSql('DROP TABLE equipement');
        $this->addSql('DROP TABLE hebergement');
        $this->addSql('DROP TABLE hebergement_equipement');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE rental');
        $this->addSql('DROP TABLE saison');
        $this->addSql('DROP TABLE tarif');
        $this->addSql('DROP TABLE type');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
