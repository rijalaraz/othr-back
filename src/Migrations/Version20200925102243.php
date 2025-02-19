<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200925102243 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("INSERT INTO `zone` (`id`, `code`, `name`, `country`, `created_at`, `updated_at`, `deleted_at`) VALUES
        (1, '01', 'Ain', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (2, '02', 'Aisne', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (3, '03', 'Allier', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (4, '05', 'Hautes-Alpes', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (5, '04', 'Alpes-de-Haute-Provence', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (6, '06', 'Alpes-Maritimes', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (7, '07', 'Ardèche', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (8, '08', 'Ardennes', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (9, '09', 'Ariège', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (10, '10', 'Aube', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (11, '11', 'Aude', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (12, '12', 'Aveyron', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (13, '13', 'Bouches-du-Rhône', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (14, '14', 'Calvados', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (15, '15', 'Cantal', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (16, '16', 'Charente', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (17, '17', 'Charente-Maritime', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (18, '18', 'Cher', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (19, '19', 'Corrèze', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (20, '2a', 'Corse-du-sud', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (21, '2b', 'Haute-corse', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (22, '21', 'Côte-d\'or', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (23, '22', 'Côtes-d\'armor', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (24, '23', 'Creuse', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (25, '24', 'Dordogne', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (26, '25', 'Doubs', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (27, '26', 'Drôme', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (28, '27', 'Eure', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (29, '28', 'Eure-et-Loir', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (30, '29', 'Finistère', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (31, '30', 'Gard', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (32, '31', 'Haute-Garonne', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (33, '32', 'Gers', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (34, '33', 'Gironde', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (35, '34', 'Hérault', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (36, '35', 'Ile-et-Vilaine', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (37, '36', 'Indre', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (38, '37', 'Indre-et-Loire', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (39, '38', 'Isère', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (40, '39', 'Jura', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (41, '40', 'Landes', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (42, '41', 'Loir-et-Cher', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (43, '42', 'Loire', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (44, '43', 'Haute-Loire', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (45, '44', 'Loire-Atlantique', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (46, '45', 'Loiret', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (47, '46', 'Lot', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (48, '47', 'Lot-et-Garonne', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (49, '48', 'Lozère', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (50, '49', 'Maine-et-Loire', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (51, '50', 'Manche', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (52, '51', 'Marne', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (53, '52', 'Haute-Marne', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (54, '53', 'Mayenne', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (55, '54', 'Meurthe-et-Moselle', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (56, '55', 'Meuse', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (57, '56', 'Morbihan', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (58, '57', 'Moselle', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (59, '58', 'Nièvre', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (60, '59', 'Nord', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (61, '60', 'Oise', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (62, '61', 'Orne', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (63, '62', 'Pas-de-Calais', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (64, '63', 'Puy-de-Dôme', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (65, '64', 'Pyrénées-Atlantiques', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (66, '65', 'Hautes-Pyrénées', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (67, '66', 'Pyrénées-Orientales', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (68, '67', 'Bas-Rhin', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (69, '68', 'Haut-Rhin', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (70, '69', 'Rhône', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (71, '70', 'Haute-Saône', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (72, '71', 'Saône-et-Loire', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (73, '72', 'Sarthe', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (74, '73', 'Savoie', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (75, '74', 'Haute-Savoie', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (76, '75', 'Paris', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (77, '76', 'Seine-Maritime', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (78, '77', 'Seine-et-Marne', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (79, '78', 'Yvelines', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (80, '79', 'Deux-Sèvres', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (81, '80', 'Somme', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (82, '81', 'Tarn', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (83, '82', 'Tarn-et-Garonne', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (84, '83', 'Var', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (85, '84', 'Vaucluse', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (86, '85', 'Vendée', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (87, '86', 'Vienne', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (88, '87', 'Haute-Vienne', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (89, '88', 'Vosges', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (90, '89', 'Yonne', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (91, '90', 'Territoire de Belfort', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (92, '91', 'Essonne', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (93, '92', 'Hauts-de-Seine', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (94, '93', 'Seine-Saint-Denis', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (95, '94', 'Val-de-Marne', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (96, '95', 'Val-d\'oise', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (97, '976', 'Mayotte', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (98, '971', 'Guadeloupe', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (99, '973', 'Guyane', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (100, '972', 'Martinique', 'FR', '2020-09-24 12:29:12', NULL, NULL),
        (101, '974', 'Réunion', 'FR', '2020-09-24 12:29:12', NULL, NULL)");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("DELETE FROM `zone`");
    }
}
