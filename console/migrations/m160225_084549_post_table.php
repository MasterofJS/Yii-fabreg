<?php

use console\migrations\Migration;

class m160225_084549_post_table extends Migration
{
    public function up()
    {
        $this->createTable(self::TABLE_POST, [
            'id' => 'BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'user_id' => 'BIGINT UNSIGNED NOT NULL',
            'description' => $this->string(255)->notNull(),
            'photo_id' => 'BIGINT UNSIGNED NULL',
            'is_nsfw' => $this->boolean()->defaultValue(false),
            'status' => 'TINYINT DEFAULT 6',
            'channel' => 'TINYINT DEFAULT 0',
            'is_retired' => $this->boolean()->defaultValue(false),
            'released_at' => $this->dateTime()->defaultValue(null),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ], $this->tableOptions);
        $this->createIndex(null, self::TABLE_POST, 'status');
        $this->createIndex(null, self::TABLE_POST, 'channel');
        $this->addForeignKey(null, self::TABLE_POST, 'user_id', self::TABLE_USER, 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, self::TABLE_POST, 'photo_id', self::TABLE_IMAGE, 'id', 'SET NULL', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable(self::TABLE_POST);
    }
}
