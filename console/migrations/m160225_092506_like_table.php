<?php

use console\migrations\Migration;

class m160225_092506_like_table extends Migration
{
    public function up()
    {
        $this->createTable(self::TABLE_LIKE, [
            'id' => 'BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'user_id' => 'BIGINT UNSIGNED NOT NULL',
            'entity_id' => 'BIGINT UNSIGNED NOT NULL',
            'entity_type' => 'TINYINT UNSIGNED DEFAULT 0',
            'value' => 'TINYINT DEFAULT 1',
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ], $this->tableOptions);

        $this->createIndex(null, self::TABLE_LIKE, 'entity_id');
        $this->createIndex(null, self::TABLE_LIKE, 'entity_type');
        $this->createIndex(null, self::TABLE_LIKE, 'value');
        $this->addForeignKey(null, self::TABLE_LIKE, 'user_id', self::TABLE_USER, 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable(self::TABLE_LIKE);
    }
}
