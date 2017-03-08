<?php

use console\migrations\Migration;

class m160225_084618_comment_table extends Migration
{
    public function up()
    {
        $this->createTable(self::TABLE_COMMENT, [
            'id' => 'BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'parent_id' => 'BIGINT UNSIGNED  NULL',
            'reply_id' => 'BIGINT UNSIGNED  NULL',
            'user_id' => 'BIGINT UNSIGNED NOT NULL',
            'post_id' => 'BIGINT UNSIGNED NOT NULL',
            'content' => $this->string(500)->notNull(),
            'type' => 'TINYINT UNSIGNED DEFAULT 0',
            'status' => 'TINYINT DEFAULT 6',
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ], $this->tableOptions);

        $this->addForeignKey(null, self::TABLE_COMMENT, 'user_id', self::TABLE_USER, 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, self::TABLE_COMMENT, 'post_id', self::TABLE_POST, 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, self::TABLE_COMMENT, 'parent_id', self::TABLE_COMMENT, 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, self::TABLE_COMMENT, 'reply_id', self::TABLE_COMMENT, 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable(self::TABLE_COMMENT);
    }
}
