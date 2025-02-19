<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200924083216 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("INSERT INTO `working_sector` (`id`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES
        (1, 'Artiste', '2020-09-25 16:12:42', NULL, NULL),
        (2, 'Bien être', '2020-09-25 16:12:42', NULL, NULL),
        (3, 'BTP', '2020-09-25 16:12:42', NULL, NULL),
        (4, 'Commerçant', '2020-09-25 16:12:42', NULL, NULL),
        (5, 'Communication', '2020-09-25 16:12:42', NULL, NULL),
        (6, 'Digital', '2020-09-25 16:12:42', NULL, NULL),
        (7, 'Finance', '2020-09-25 16:12:42', NULL, NULL),
        (8, 'Immo', '2020-09-25 16:12:42', NULL, NULL),
        (9, 'Informatique', '2020-09-25 16:12:42', NULL, NULL),
        (10, 'RH', '2020-09-25 16:12:42', NULL, NULL),
        (11, 'Service aux entreprises', '2020-09-25 16:12:42', NULL, NULL),
        (12, 'Service aux particuliers', '2020-09-25 16:12:42', NULL, NULL),
        (13, 'Sport', '2020-09-25 16:12:42', NULL, NULL),
        (14, 'AUTRE...', '2020-09-25 16:12:42', NULL, NULL)");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("DELETE FROM `working_sector`");
    }
}
