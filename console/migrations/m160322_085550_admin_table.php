<?php

use console\migrations\Migration;

class m160322_085550_admin_table extends Migration
{
    public function up()
    {
        $this->createTable(self::TABLE_ADMIN, [
            'id' => 'INT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'username' => $this->string(31)->notNull()->unique(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string(255)->notNull(),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
            'last_login' => $this->dateTime()->defaultValue(null),
        ], $this->tableOptions);
        $this->createOne();

    }

    public function down()
    {
        $this->dropTable(self::TABLE_ADMIN);
    }

    public function createOne()
    {
        $this->insert(self::TABLE_ADMIN, [
            'username' => 'admin',
            'password_hash' => Yii::$app->security->generatePasswordHash('bigdrop'),
            'auth_key' => Yii::$app->security->generateRandomString(),
            'created_at' => \common\models\ActiveRecord::createDateTime(),
            'updated_at' => \common\models\ActiveRecord::createDateTime(),
        ]);
    }
}
