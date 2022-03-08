<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220223142747 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE holiday_country (holiday_id INT NOT NULL, country_id INT NOT NULL, INDEX IDX_61AE8C68830A3EC0 (holiday_id), INDEX IDX_61AE8C68F92F3E70 (country_id), PRIMARY KEY(holiday_id, country_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE holiday_country ADD CONSTRAINT FK_61AE8C68830A3EC0 FOREIGN KEY (holiday_id) REFERENCES holiday (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE holiday_country ADD CONSTRAINT FK_61AE8C68F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE holiday ADD date DATE NOT NULL, ADD name VARCHAR(255) NOT NULL, ADD type VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE holiday_country');
        $this->addSql('ALTER TABLE country CHANGE name name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE country_code country_code VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE holiday DROP date, DROP name, DROP type');
    }
}
