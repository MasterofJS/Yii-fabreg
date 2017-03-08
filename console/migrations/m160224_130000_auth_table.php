<?php

use console\migrations\Migration;

class m160224_130000_auth_table extends Migration
{
    public function up()
    {

        $this->createTable(self::TABLE_USER_AUTH, [
            'id' => 'BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'user_id' => 'BIGINT UNSIGNED NOT NULL',
            'source' => $this->string(255)->notNull(),
            'source_id' => $this->string(255)->notNull(),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ], $this->tableOptions);

        $this->addForeignKey(null, self::TABLE_USER_AUTH, 'user_id', self::TABLE_USER, 'id', 'CASCADE', 'CASCADE');
    }


    public function down()
    {
        $this->dropTable(self::TABLE_USER_AUTH);
    }
}
