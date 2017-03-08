<?php

use console\migrations\Migration;

class m160413_123940_task_table extends Migration
{
    public function up()
    {
        $this->createTable(self::TABLE_TASK, [
            'id' => 'BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'data' => $this->text()->notNull(),
            'action' => $this->string(31)->notNull(),
            'options' => $this->text()->defaultValue(null),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ], $this->tableOptions);

        $this->createIndex(null, self::TABLE_TASK, 'action');
    }

    public function down()
    {
        $this->dropTable(self::TABLE_TASK);
    }
}
