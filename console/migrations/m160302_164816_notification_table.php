<?php

use console\migrations\Migration;

class m160302_164816_notification_table extends Migration
{
    public function up()
    {
        $this->createTable(self::TABLE_NOTIFICATION, [
            'id' => 'BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'receiver_id' => $this->bigInteger()->unsigned()->notNull(),
            'actor_id' => $this->bigInteger()->unsigned()->defaultValue(null),
            'last_actor_id' => $this->bigInteger()->unsigned()->defaultValue(null),
            'last_count' => $this->bigInteger()->unsigned(),
            'entity_id' => $this->bigInteger()->unsigned()->notNull(),
            'entity_type' => 'TINYINT UNSIGNED DEFAULT 0',
            'type' => 'TINYINT UNSIGNED DEFAULT 0',
            'is_read' => $this->boolean()->defaultValue(false),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ], $this->tableOptions);
        $this->addForeignKey(null, self::TABLE_NOTIFICATION, 'actor_id', self::TABLE_USER, 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey(null, self::TABLE_NOTIFICATION, 'last_actor_id', self::TABLE_USER, 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey(null, self::TABLE_NOTIFICATION, 'receiver_id', self::TABLE_USER, 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable(self::TABLE_NOTIFICATION);
    }

}
