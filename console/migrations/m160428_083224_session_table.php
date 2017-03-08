<?php

use console\migrations\Migration;

class m160428_083224_session_table extends Migration
{
    public function up()
    {
        $this->createTable(self::TABLE_USER_SESSION, [
            'id' => 'CHAR(40) NOT NULL PRIMARY KEY',
            'expire' => 'INT',
            'data' => 'LONGBLOB',
        ], $this->tableOptions);

        $this->createIndex(null, self::TABLE_USER_SESSION, 'expire');
    }

    public function down()
    {
        $this->dropTable(self::TABLE_USER_SESSION);
    }
}
