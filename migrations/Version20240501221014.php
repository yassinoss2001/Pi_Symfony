<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240501221014 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE favoris_evennemnt DROP FOREIGN KEY FK_652C7ABB51E8871B');
        $this->addSql('ALTER TABLE favoris_evennemnt DROP FOREIGN KEY FK_652C7ABB8D06FF4F');
        $this->addSql('ALTER TABLE favoris_user DROP FOREIGN KEY FK_3E144C2EA76ED395');
        $this->addSql('ALTER TABLE favoris_user DROP FOREIGN KEY FK_3E144C2E51E8871B');
        $this->addSql('DROP TABLE favoris_evennemnt');
        $this->addSql('DROP TABLE favoris_user');
        $this->addSql('ALTER TABLE favoris ADD evennemnt_id INT DEFAULT NULL, ADD user_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE favoris ADD CONSTRAINT FK_8933C4328D06FF4F FOREIGN KEY (evennemnt_id) REFERENCES evennemnt (id)');
        $this->addSql('ALTER TABLE favoris ADD CONSTRAINT FK_8933C4329D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_8933C4328D06FF4F ON favoris (evennemnt_id)');
        $this->addSql('CREATE INDEX IDX_8933C4329D86650F ON favoris (user_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE favoris_evennemnt (favoris_id INT NOT NULL, evennemnt_id INT NOT NULL, INDEX IDX_652C7ABB51E8871B (favoris_id), INDEX IDX_652C7ABB8D06FF4F (evennemnt_id), PRIMARY KEY(favoris_id, evennemnt_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE favoris_user (favoris_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_3E144C2E51E8871B (favoris_id), INDEX IDX_3E144C2EA76ED395 (user_id), PRIMARY KEY(favoris_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE favoris_evennemnt ADD CONSTRAINT FK_652C7ABB51E8871B FOREIGN KEY (favoris_id) REFERENCES favoris (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE favoris_evennemnt ADD CONSTRAINT FK_652C7ABB8D06FF4F FOREIGN KEY (evennemnt_id) REFERENCES evennemnt (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE favoris_user ADD CONSTRAINT FK_3E144C2EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE favoris_user ADD CONSTRAINT FK_3E144C2E51E8871B FOREIGN KEY (favoris_id) REFERENCES favoris (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE favoris DROP FOREIGN KEY FK_8933C4328D06FF4F');
        $this->addSql('ALTER TABLE favoris DROP FOREIGN KEY FK_8933C4329D86650F');
        $this->addSql('DROP INDEX IDX_8933C4328D06FF4F ON favoris');
        $this->addSql('DROP INDEX IDX_8933C4329D86650F ON favoris');
        $this->addSql('ALTER TABLE favoris DROP evennemnt_id, DROP user_id_id');
    }
}
