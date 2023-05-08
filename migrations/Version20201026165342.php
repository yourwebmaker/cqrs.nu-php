<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201026165342 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Creates default table schema';
    }

    public function up(Schema $schema) : void
    {
        //Tab aggregate table
        $this->addSql('
            CREATE TABLE IF NOT EXISTS `aggregate_tab` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `event_id` BINARY(16) NOT NULL,
                `aggregate_root_id` BINARY(16) NOT NULL,
                `version` int(20) unsigned NULL,
                `payload` varchar(16001) NOT NULL,
                PRIMARY KEY (`id` ASC),
                KEY `reconstitution` (`aggregate_root_id`, `version` ASC)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB;
        ');

        //Tab Read Model
        $this->addSql('
            create table read_model_tab (
                    tab_id varchar(36) not null,
                    table_number int not null,
                    waiter text not null
            );
        ');

        //Tab Items read model
        $this->addSql('
            create table read_model_tab_item (
                tab_id varchar(36) not null,
                menu_number int not null,
                description text not null,
                price decimal not null ,
                status char(20) not null 
            );
        ');

        //Cheftodo read models.
        $this->addSql('
            create table read_model_chef_todo_group (
                tab_id varchar(36) not null,
                group_id varchar(36) not null
            );
        ');

        $this->addSql('
            create table read_model_chef_todo_item (
                group_id varchar(36) not null,
                description varchar(100) null,
                menu_number int null,
                tab_id varchar(36) not null
            );
        ');
    }

    public function down(Schema $schema) : void
    {
        $this->throwIrreversibleMigrationException();
    }
}
