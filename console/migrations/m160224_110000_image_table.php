<?php

use console\migrations\Migration;

class m160224_110000_image_table extends Migration
{
    public function up()
    {
        $this->createTable(self::TABLE_IMAGE, [
            'id' => 'BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'name' => $this->string(255)->unique()->notNull(),
            'extension' => $this->string(5)->notNull(),
            'type' => $this->string(31)->notNull(),
            'size' => $this->integer()->notNull(),
            'is_default' => $this->boolean()->defaultValue(false),
            'is_over_max_height' => $this->boolean()->defaultValue(false),
            'is_uploaded_to_cdn' => $this->boolean()->defaultValue(false),
            'target' => 'TINYINT DEFAULT ' . \common\models\Media::TYPE,
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ], $this->tableOptions);
        $this->importDefaults();

    }


    public function down()
    {
        $this->dropTable(self::TABLE_IMAGE);
    }

    public function importDefaults()
    {
        $path = Yii::getAlias('@frontend/web/static/dist/images/avatars');
        $default = Yii::getAlias(\common\models\Avatar::LOCALE_DEFAULT_BASE_PATH);
        $files = \common\helpers\FileHelper::findFiles($path);
        $data = [];
        $date = \common\models\ActiveRecord::createDate();
        foreach ($files as $file) {
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            $data[] = [basename($file, ".$ext"), \common\helpers\FileHelper::getMimeType($file), $ext, filesize($file), true, \common\models\Avatar::TYPE, $date, $date];
            \common\helpers\FileHelper::copyFromPath($file, $default . DIRECTORY_SEPARATOR . basename($file));
            \common\helpers\FileHelper::chmod($default . DIRECTORY_SEPARATOR . basename($file));
        }
        $this->batchInsert(self::TABLE_IMAGE, ['name', 'type', 'extension', 'size', 'is_default', 'target', 'created_at', 'updated_at'], $data);
    }
}
