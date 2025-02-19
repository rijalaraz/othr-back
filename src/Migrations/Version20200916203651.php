<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200916203651 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE network (id INT AUTO_INCREMENT NOT NULL, address_id INT NOT NULL, image_id INT DEFAULT NULL, type_id INT NOT NULL, logo_id INT DEFAULT NULL, image_representation_id INT DEFAULT NULL, video_id INT DEFAULT NULL, image_description_id INT DEFAULT NULL, zone_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description_who LONGTEXT NOT NULL, description_why LONGTEXT NOT NULL, description_how LONGTEXT NOT NULL, email VARCHAR(255) NOT NULL, website VARCHAR(255) NOT NULL, nb_members_offline INT NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_608487BCF5B7AF75 (address_id), UNIQUE INDEX UNIQ_608487BC3DA5256D (image_id), INDEX IDX_608487BCC54C8C93 (type_id), UNIQUE INDEX UNIQ_608487BCF98F144A (logo_id), UNIQUE INDEX UNIQ_608487BCD49BA93C (image_representation_id), UNIQUE INDEX UNIQ_608487BC29C1004E (video_id), UNIQUE INDEX UNIQ_608487BC9A094101 (image_description_id), INDEX IDX_608487BC9F2C3FAB (zone_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, amount NUMERIC(10, 2) NOT NULL, payment_status VARCHAR(25) NOT NULL, currency VARCHAR(3) NOT NULL, stripe_payment_intent VARCHAR(255) DEFAULT NULL, stripe_payment_error JSON DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_6D28840DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_token_device (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, token LONGTEXT NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_889B658EA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE advert (id INT AUTO_INCREMENT NOT NULL, image_id INT DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_54F1F40B3DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media (id INT AUTO_INCREMENT NOT NULL, event_id INT DEFAULT NULL, post_id INT DEFAULT NULL, activity_user_id INT DEFAULT NULL, customer_user_id INT DEFAULT NULL, user_id INT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, url LONGTEXT NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_6A2CA10C71F7E88B (event_id), UNIQUE INDEX UNIQ_6A2CA10C4B89032C (post_id), INDEX IDX_6A2CA10CA73CA575 (activity_user_id), INDEX IDX_6A2CA10CBBB3772B (customer_user_id), INDEX IDX_6A2CA10CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zone (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(10) NOT NULL, name VARCHAR(255) NOT NULL, country VARCHAR(2) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ticket (id INT AUTO_INCREMENT NOT NULL, event_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, price NUMERIC(10, 2) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_97A0ADA371F7E88B (event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post_view (id INT AUTO_INCREMENT NOT NULL, post_id INT DEFAULT NULL, user_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_37A8CC854B89032C (post_id), INDEX IDX_37A8CC85A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, image_id INT DEFAULT NULL, color_id INT DEFAULT NULL, working_sector_id INT DEFAULT NULL, video_id INT DEFAULT NULL, logo_id INT DEFAULT NULL, address_id INT DEFAULT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, job VARCHAR(255) NOT NULL, phone_number VARCHAR(255) DEFAULT NULL, email VARCHAR(180) NOT NULL, description LONGTEXT DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, token VARCHAR(255) DEFAULT NULL, stripe_customer VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D6493DA5256D (image_id), INDEX IDX_8D93D6497ADA1FB5 (color_id), INDEX IDX_8D93D64916349BD6 (working_sector_id), UNIQUE INDEX UNIQ_8D93D64929C1004E (video_id), UNIQUE INDEX UNIQ_8D93D649F98F144A (logo_id), UNIQUE INDEX UNIQ_8D93D649F5B7AF75 (address_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subscriber_subscription (subscriber_id INT NOT NULL, subscription_id INT NOT NULL, INDEX IDX_61B4A9AF7808B1AD (subscriber_id), INDEX IDX_61B4A9AF9A1887DC (subscription_id), PRIMARY KEY(subscriber_id, subscription_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_zone (user_id INT NOT NULL, zone_id INT NOT NULL, INDEX IDX_DA6A8CCEA76ED395 (user_id), INDEX IDX_DA6A8CCE9F2C3FAB (zone_id), PRIMARY KEY(user_id, zone_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE address (id INT AUTO_INCREMENT NOT NULL, zip_code VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, street VARCHAR(255) NOT NULL, place VARCHAR(255) DEFAULT NULL, info VARCHAR(255) DEFAULT NULL, region VARCHAR(255) DEFAULT NULL, country VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE argument (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_D113B0AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, image_id INT DEFAULT NULL, network_id INT DEFAULT NULL, category_id INT DEFAULT NULL, user_id INT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, content LONGTEXT DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_5A8A6C8D3DA5256D (image_id), INDEX IDX_5A8A6C8D34128B91 (network_id), INDEX IDX_5A8A6C8D12469DE2 (category_id), INDEX IDX_5A8A6C8DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sponsorship (id INT AUTO_INCREMENT NOT NULL, sponsor_id INT DEFAULT NULL, sponsored_id INT DEFAULT NULL, amount DOUBLE PRECISION DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_C0F10CD412F7FB51 (sponsor_id), INDEX IDX_C0F10CD473340AD (sponsored_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE network_member (id INT AUTO_INCREMENT NOT NULL, network_id INT NOT NULL, user_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_12F7FD1E34128B91 (network_id), INDEX IDX_12F7FD1EA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE color (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(7) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_event (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, event_id INT DEFAULT NULL, ticket_type_id INT DEFAULT NULL, payment_id INT NOT NULL, registration_date DATETIME NOT NULL, nb_places INT NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_D96CF1FFA76ED395 (user_id), INDEX IDX_D96CF1FF71F7E88B (event_id), INDEX IDX_D96CF1FFC980D5C1 (ticket_type_id), UNIQUE INDEX UNIQ_D96CF1FF4C3A3BB (payment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post_element (id INT AUTO_INCREMENT NOT NULL, image_id INT DEFAULT NULL, post_id INT DEFAULT NULL, text LONGTEXT DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, orders VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_7CA8E90A3DA5256D (image_id), INDEX IDX_7CA8E90A4B89032C (post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, sender_id INT DEFAULT NULL, receiver_id INT NOT NULL, type VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, status VARCHAR(255) DEFAULT NULL, metadata JSON DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_BF5476CAF624B39D (sender_id), INDEX IDX_BF5476CACD53EDB6 (receiver_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE working_sector (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE network_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, color VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE relationship (id INT AUTO_INCREMENT NOT NULL, source_user_id INT NOT NULL, target_user_id INT NOT NULL, team TINYINT(1) DEFAULT \'0\' NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_200444A0EEB16BFD (source_user_id), INDEX IDX_200444A06C066AFE (target_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, address_id INT NOT NULL, image_id INT DEFAULT NULL, user_id INT NOT NULL, network_id INT DEFAULT NULL, zone_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, nb_tickets SMALLINT NOT NULL, start_date DATETIME DEFAULT NULL, end_date DATETIME DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_3BAE0AA7F5B7AF75 (address_id), UNIQUE INDEX UNIQ_3BAE0AA73DA5256D (image_id), INDEX IDX_3BAE0AA7A76ED395 (user_id), INDEX IDX_3BAE0AA734128B91 (network_id), INDEX IDX_3BAE0AA79F2C3FAB (zone_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_E19D9AD2A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE activity (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_AC74095AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE network ADD CONSTRAINT FK_608487BCF5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id)');
        $this->addSql('ALTER TABLE network ADD CONSTRAINT FK_608487BC3DA5256D FOREIGN KEY (image_id) REFERENCES media (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE network ADD CONSTRAINT FK_608487BCC54C8C93 FOREIGN KEY (type_id) REFERENCES network_type (id)');
        $this->addSql('ALTER TABLE network ADD CONSTRAINT FK_608487BCF98F144A FOREIGN KEY (logo_id) REFERENCES media (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE network ADD CONSTRAINT FK_608487BCD49BA93C FOREIGN KEY (image_representation_id) REFERENCES media (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE network ADD CONSTRAINT FK_608487BC29C1004E FOREIGN KEY (video_id) REFERENCES media (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE network ADD CONSTRAINT FK_608487BC9A094101 FOREIGN KEY (image_description_id) REFERENCES media (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE network ADD CONSTRAINT FK_608487BC9F2C3FAB FOREIGN KEY (zone_id) REFERENCES zone (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_token_device ADD CONSTRAINT FK_889B658EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE advert ADD CONSTRAINT FK_54F1F40B3DA5256D FOREIGN KEY (image_id) REFERENCES media (id)');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10C71F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10C4B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10CA73CA575 FOREIGN KEY (activity_user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10CBBB3772B FOREIGN KEY (customer_user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA371F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE post_view ADD CONSTRAINT FK_37A8CC854B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE post_view ADD CONSTRAINT FK_37A8CC85A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6493DA5256D FOREIGN KEY (image_id) REFERENCES media (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6497ADA1FB5 FOREIGN KEY (color_id) REFERENCES color (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64916349BD6 FOREIGN KEY (working_sector_id) REFERENCES working_sector (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64929C1004E FOREIGN KEY (video_id) REFERENCES media (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649F98F144A FOREIGN KEY (logo_id) REFERENCES media (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649F5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id)');
        $this->addSql('ALTER TABLE subscriber_subscription ADD CONSTRAINT FK_61B4A9AF7808B1AD FOREIGN KEY (subscriber_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE subscriber_subscription ADD CONSTRAINT FK_61B4A9AF9A1887DC FOREIGN KEY (subscription_id) REFERENCES network (id)');
        $this->addSql('ALTER TABLE user_zone ADD CONSTRAINT FK_DA6A8CCEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_zone ADD CONSTRAINT FK_DA6A8CCE9F2C3FAB FOREIGN KEY (zone_id) REFERENCES zone (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE argument ADD CONSTRAINT FK_D113B0AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D3DA5256D FOREIGN KEY (image_id) REFERENCES media (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D34128B91 FOREIGN KEY (network_id) REFERENCES network (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE sponsorship ADD CONSTRAINT FK_C0F10CD412F7FB51 FOREIGN KEY (sponsor_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE sponsorship ADD CONSTRAINT FK_C0F10CD473340AD FOREIGN KEY (sponsored_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE network_member ADD CONSTRAINT FK_12F7FD1E34128B91 FOREIGN KEY (network_id) REFERENCES network (id)');
        $this->addSql('ALTER TABLE network_member ADD CONSTRAINT FK_12F7FD1EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_event ADD CONSTRAINT FK_D96CF1FFA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_event ADD CONSTRAINT FK_D96CF1FF71F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE user_event ADD CONSTRAINT FK_D96CF1FFC980D5C1 FOREIGN KEY (ticket_type_id) REFERENCES ticket (id)');
        $this->addSql('ALTER TABLE user_event ADD CONSTRAINT FK_D96CF1FF4C3A3BB FOREIGN KEY (payment_id) REFERENCES payment (id)');
        $this->addSql('ALTER TABLE post_element ADD CONSTRAINT FK_7CA8E90A3DA5256D FOREIGN KEY (image_id) REFERENCES media (id)');
        $this->addSql('ALTER TABLE post_element ADD CONSTRAINT FK_7CA8E90A4B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAF624B39D FOREIGN KEY (sender_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CACD53EDB6 FOREIGN KEY (receiver_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE relationship ADD CONSTRAINT FK_200444A0EEB16BFD FOREIGN KEY (source_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE relationship ADD CONSTRAINT FK_200444A06C066AFE FOREIGN KEY (target_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7F5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA73DA5256D FOREIGN KEY (image_id) REFERENCES media (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA734128B91 FOREIGN KEY (network_id) REFERENCES network (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA79F2C3FAB FOREIGN KEY (zone_id) REFERENCES zone (id)');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD2A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE subscriber_subscription DROP FOREIGN KEY FK_61B4A9AF9A1887DC');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D34128B91');
        $this->addSql('ALTER TABLE network_member DROP FOREIGN KEY FK_12F7FD1E34128B91');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA734128B91');
        $this->addSql('ALTER TABLE user_event DROP FOREIGN KEY FK_D96CF1FF4C3A3BB');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D12469DE2');
        $this->addSql('ALTER TABLE network DROP FOREIGN KEY FK_608487BC3DA5256D');
        $this->addSql('ALTER TABLE network DROP FOREIGN KEY FK_608487BCF98F144A');
        $this->addSql('ALTER TABLE network DROP FOREIGN KEY FK_608487BCD49BA93C');
        $this->addSql('ALTER TABLE network DROP FOREIGN KEY FK_608487BC29C1004E');
        $this->addSql('ALTER TABLE network DROP FOREIGN KEY FK_608487BC9A094101');
        $this->addSql('ALTER TABLE advert DROP FOREIGN KEY FK_54F1F40B3DA5256D');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6493DA5256D');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64929C1004E');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649F98F144A');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D3DA5256D');
        $this->addSql('ALTER TABLE post_element DROP FOREIGN KEY FK_7CA8E90A3DA5256D');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA73DA5256D');
        $this->addSql('ALTER TABLE network DROP FOREIGN KEY FK_608487BC9F2C3FAB');
        $this->addSql('ALTER TABLE user_zone DROP FOREIGN KEY FK_DA6A8CCE9F2C3FAB');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA79F2C3FAB');
        $this->addSql('ALTER TABLE user_event DROP FOREIGN KEY FK_D96CF1FFC980D5C1');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840DA76ED395');
        $this->addSql('ALTER TABLE user_token_device DROP FOREIGN KEY FK_889B658EA76ED395');
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10CA73CA575');
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10CBBB3772B');
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10CA76ED395');
        $this->addSql('ALTER TABLE post_view DROP FOREIGN KEY FK_37A8CC85A76ED395');
        $this->addSql('ALTER TABLE subscriber_subscription DROP FOREIGN KEY FK_61B4A9AF7808B1AD');
        $this->addSql('ALTER TABLE user_zone DROP FOREIGN KEY FK_DA6A8CCEA76ED395');
        $this->addSql('ALTER TABLE argument DROP FOREIGN KEY FK_D113B0AA76ED395');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DA76ED395');
        $this->addSql('ALTER TABLE sponsorship DROP FOREIGN KEY FK_C0F10CD412F7FB51');
        $this->addSql('ALTER TABLE sponsorship DROP FOREIGN KEY FK_C0F10CD473340AD');
        $this->addSql('ALTER TABLE network_member DROP FOREIGN KEY FK_12F7FD1EA76ED395');
        $this->addSql('ALTER TABLE user_event DROP FOREIGN KEY FK_D96CF1FFA76ED395');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAF624B39D');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CACD53EDB6');
        $this->addSql('ALTER TABLE relationship DROP FOREIGN KEY FK_200444A0EEB16BFD');
        $this->addSql('ALTER TABLE relationship DROP FOREIGN KEY FK_200444A06C066AFE');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7A76ED395');
        $this->addSql('ALTER TABLE service DROP FOREIGN KEY FK_E19D9AD2A76ED395');
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095AA76ED395');
        $this->addSql('ALTER TABLE network DROP FOREIGN KEY FK_608487BCF5B7AF75');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649F5B7AF75');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7F5B7AF75');
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10C4B89032C');
        $this->addSql('ALTER TABLE post_view DROP FOREIGN KEY FK_37A8CC854B89032C');
        $this->addSql('ALTER TABLE post_element DROP FOREIGN KEY FK_7CA8E90A4B89032C');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6497ADA1FB5');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64916349BD6');
        $this->addSql('ALTER TABLE network DROP FOREIGN KEY FK_608487BCC54C8C93');
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10C71F7E88B');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA371F7E88B');
        $this->addSql('ALTER TABLE user_event DROP FOREIGN KEY FK_D96CF1FF71F7E88B');
        $this->addSql('DROP TABLE network');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE user_token_device');
        $this->addSql('DROP TABLE advert');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP TABLE zone');
        $this->addSql('DROP TABLE ticket');
        $this->addSql('DROP TABLE post_view');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE subscriber_subscription');
        $this->addSql('DROP TABLE user_zone');
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE argument');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE sponsorship');
        $this->addSql('DROP TABLE network_member');
        $this->addSql('DROP TABLE color');
        $this->addSql('DROP TABLE user_event');
        $this->addSql('DROP TABLE post_element');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE working_sector');
        $this->addSql('DROP TABLE network_type');
        $this->addSql('DROP TABLE relationship');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE activity');
    }
}
