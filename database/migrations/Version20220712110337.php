<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220712110337 extends AbstractMigration
{
    private const TABLE_POST_CATEGORY = 'post_categories';
    private const TABLE_POST = 'post_posts';

    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable(self::TABLE_POST);
        $table->addColumn('id', Types::GUID);
        $table->addColumn('category_id', Types::GUID);
        $table->addColumn('title', Types::STRING);
        $table->addColumn('text', Types::TEXT, ['notnull' => false]);
        $table->addColumn('created_at', Types::DATETIME_IMMUTABLE, ['notnull' => false]);
        $table->addColumn('updated_at', Types::DATETIME_IMMUTABLE, ['notnull' => false]);
        $table->addColumn('deleted_at', Types::DATETIME_IMMUTABLE, ['notnull' => false]);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['category_id'], 'post_posts_category_id_index');
        $table->addForeignKeyConstraint(self::TABLE_POST_CATEGORY, ['category_id'], ['id'], ['onDelete' => 'CASCADE'], 'post_posts_category_id_foreign');
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable(self::TABLE_POST);
    }
}
