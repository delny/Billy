<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181124121015 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE family (id INT AUTO_INCREMENT NOT NULL, father_id INT DEFAULT NULL, mother_id INT DEFAULT NULL, fid VARCHAR(255) NOT NULL, INDEX IDX_A5E6215B2055B9A2 (father_id), INDEX IDX_A5E6215BB78A354D (mother_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE family ADD CONSTRAINT FK_A5E6215B2055B9A2 FOREIGN KEY (father_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE family ADD CONSTRAINT FK_A5E6215BB78A354D FOREIGN KEY (mother_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE person ADD parents_family_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD17645076E37 FOREIGN KEY (parents_family_id) REFERENCES family (id)');
        $this->addSql('CREATE INDEX IDX_34DCD17645076E37 ON person (parents_family_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD17645076E37');
        $this->addSql('DROP TABLE family');
        $this->addSql('DROP INDEX IDX_34DCD17645076E37 ON person');
        $this->addSql('ALTER TABLE person DROP parents_family_id');
    }
}
