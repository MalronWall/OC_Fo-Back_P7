<?php

declare(strict_types=1);

namespace AppBundle\Domain\DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190522144142 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE client (id INT NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_C7440455F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE clients_phones (client_id INT NOT NULL, phone_id INT NOT NULL, INDEX IDX_E6FFFA1219EB6921 (client_id), INDEX IDX_E6FFFA123B7323CB (phone_id), PRIMARY KEY(client_id, phone_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE clients_users (client_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_C300512E19EB6921 (client_id), INDEX IDX_C300512EA76ED395 (user_id), PRIMARY KEY(client_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE phone (id INT NOT NULL, brand VARCHAR(255) NOT NULL, model VARCHAR(255) NOT NULL, os VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, cpu VARCHAR(255) DEFAULT NULL, gpu VARCHAR(255) DEFAULT NULL, ram VARCHAR(255) DEFAULT NULL, memory VARCHAR(255) DEFAULT NULL, dimensions VARCHAR(255) DEFAULT NULL, weight VARCHAR(255) DEFAULT NULL, resolution VARCHAR(255) DEFAULT NULL, mainCamera VARCHAR(255) DEFAULT NULL, selfieCamera VARCHAR(255) DEFAULT NULL, sound VARCHAR(255) DEFAULT NULL, battery VARCHAR(255) DEFAULT NULL, colors VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT NOT NULL, firstname VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, cp VARCHAR(255) NOT NULL, phoneNumber VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE clients_phones ADD CONSTRAINT FK_E6FFFA1219EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE clients_phones ADD CONSTRAINT FK_E6FFFA123B7323CB FOREIGN KEY (phone_id) REFERENCES phone (id)');
        $this->addSql('ALTER TABLE clients_users ADD CONSTRAINT FK_C300512E19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE clients_users ADD CONSTRAINT FK_C300512EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE clients_phones DROP FOREIGN KEY FK_E6FFFA1219EB6921');
        $this->addSql('ALTER TABLE clients_users DROP FOREIGN KEY FK_C300512E19EB6921');
        $this->addSql('ALTER TABLE clients_phones DROP FOREIGN KEY FK_E6FFFA123B7323CB');
        $this->addSql('ALTER TABLE clients_users DROP FOREIGN KEY FK_C300512EA76ED395');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE clients_phones');
        $this->addSql('DROP TABLE clients_users');
        $this->addSql('DROP TABLE phone');
        $this->addSql('DROP TABLE user');
    }
}
