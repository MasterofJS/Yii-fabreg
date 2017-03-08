<?php

use console\migrations\Migration;

class m160302_164848_post_report_table extends Migration
{
    public function up()
    {
        $this->createTable(self::TABLE_POST_REPORT, [
            'user_id' => 'BIGINT UNSIGNED NOT NULL',
            'post_id' => 'BIGINT UNSIGNED NOT NULL',
            'type' => 'TINYINT UNSIGNED DEFAULT 0',
            'status' => 'TINYINT UNSIGNED DEFAULT 0',
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ], $this->tableOptions);
        $this->addPrimaryKey(null, self::TABLE_POST_REPORT, ['user_id', 'post_id']);
        $this->addForeignKey(null, self::TABLE_POST_REPORT, 'user_id', self::TABLE_USER, 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, self::TABLE_POST_REPORT, 'post_id', self::TABLE_POST, 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable(self::TABLE_POST_REPORT);
    }

}
