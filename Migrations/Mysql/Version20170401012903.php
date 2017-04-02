<?php
namespace Neos\Flow\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170401012903 extends AbstractMigration
{

    /**
     * @return string
     */
    public function getDescription()
    {
        return '';
    }

    /**
     * @param Schema $schema
     * @return void
     */
    public function up(Schema $schema)
    {
        // this up() migration is autogenerated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on "mysql".');
        
        $this->addSql('CREATE TABLE cm_neos_yubico_domain_model_key (publicid VARCHAR(12) NOT NULL, account VARCHAR(40) DEFAULT NULL, INDEX IDX_671E50F77D3656A4 (account), PRIMARY KEY(publicid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cm_neos_yubico_domain_model_key ADD CONSTRAINT FK_671E50F77D3656A4 FOREIGN KEY (account) REFERENCES neos_flow_security_account (persistence_object_identifier)');
    }

    /**
     * @param Schema $schema
     * @return void
     */
    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on "mysql".');

        $this->addSql('DROP TABLE cm_neos_yubico_domain_model_key');

    }
}