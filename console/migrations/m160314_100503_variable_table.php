<?php

use console\migrations\Migration;

class m160314_100503_variable_table extends Migration
{
    public function up()
    {
        $this->createTable(self::TABLE_VARIABLE, [
            'id' => 'INT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'namespace' => 'VARCHAR(31) NOT NULL',
            'key' => 'VARCHAR(31) NOT NULL',
            'value' => 'TEXT NOT NULL',
            'type' => 'VARCHAR(15) NOT NULL',
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),

        ], $this->tableOptions);

        $this->createIndex(null, self::TABLE_VARIABLE, ['namespace', 'key'], true);
    }

    public function down()
    {
        $this->dropTable(self::TABLE_VARIABLE);
    }
}
