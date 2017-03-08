<?php

use console\migrations\Migration;

class m160314_131402_share_table extends Migration
{
    public function up()
    {
        $this->createTable(self::TABLE_SHARE, [
            'user_id' => 'BIGINT UNSIGNED NOT NULL',
            'post_id' => 'BIGINT UNSIGNED NOT NULL',
            'network' => 'TINYINT DEFAULT 1',
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ], $this->tableOptions);
        $this->addPrimaryKey(null, self::TABLE_SHARE, ['user_id', 'post_id']);
        $this->addForeignKey(null, self::TABLE_SHARE, 'user_id', self::TABLE_USER, 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, self::TABLE_SHARE, 'post_id', self::TABLE_POST, 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable(self::TABLE_SHARE);
    }
}
