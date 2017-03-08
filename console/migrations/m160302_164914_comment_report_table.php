<?php

use console\migrations\Migration;

class m160302_164914_comment_report_table extends Migration
{
    public function up()
    {
        $this->createTable(self::TABLE_COMMENT_REPORT, [
            'user_id' => 'BIGINT UNSIGNED NOT NULL',
            'comment_id' => 'BIGINT UNSIGNED NOT NULL',
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ], $this->tableOptions);
        $this->addPrimaryKey(null, self::TABLE_COMMENT_REPORT, ['user_id', 'comment_id']);
        $this->addForeignKey(null, self::TABLE_COMMENT_REPORT, 'user_id', self::TABLE_USER, 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, self::TABLE_COMMENT_REPORT, 'comment_id', self::TABLE_COMMENT, 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable(self::TABLE_COMMENT_REPORT);
    }
}
