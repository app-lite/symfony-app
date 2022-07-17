<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220712105954 extends AbstractMigration
{
    private const TABLE_POST_CATEGORY = 'post_categories';

    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable(self::TABLE_POST_CATEGORY);
        $table->addColumn('id', Types::GUID);
        $table->addColumn('title', Types::STRING);
        $table->addColumn('description', Types::TEXT, ['notnull' => false]);
        $table->addColumn('created_at', Types::DATETIME_IMMUTABLE, ['notnull' => false]);
        $table->addColumn('updated_at', Types::DATETIME_IMMUTABLE, ['notnull' => false]);
        $table->addColumn('deleted_at', Types::DATETIME_IMMUTABLE, ['notnull' => false]);
        $table->setPrimaryKey(['id']);
        $table->addUniqueConstraint(['title'], 'post_categories_title_unique');
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable(self::TABLE_POST_CATEGORY);
    }
}
