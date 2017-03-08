<?php

use console\migrations\Migration;

class m160302_164948_user_mute_table extends Migration
{
    public function up()
    {
        $this->createTable(self::TABLE_USER_MUTE, [
            'receiver_id' => 'BIGINT UNSIGNED NOT NULL',
            'sender_id' => 'BIGINT UNSIGNED NOT NULL',
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ], $this->tableOptions);
        $this->addPrimaryKey(null, self::TABLE_USER_MUTE, ['sender_id', 'receiver_id']);
        $this->addForeignKey(null, self::TABLE_USER_MUTE, 'sender_id', self::TABLE_USER, 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, self::TABLE_USER_MUTE, 'receiver_id', self::TABLE_USER, 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable(self::TABLE_USER_MUTE);
    }

}
