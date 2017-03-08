<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 4/13/16
 * Time: 4:06 PM
 */

namespace console\controllers;

use common\models\BaseMedia;
use common\models\Task;
use yii\console\Controller;

class CloudStorageController extends Controller
{
    public function actionUpload()
    {
        /** @var $media BaseMedia */
        foreach (BaseMedia::find()->local()->each() as $media) {
            try {
                $media->instantUploadToCDN();
                $media->is_uploaded_to_cdn = true;
                $media->save();
            } catch (\Exception $e) {
                \Yii::warning($e->getMessage(), 'cnd');
            }
        }

        /** @var $task Task */
        foreach (Task::find()->uploadFromCDN()->each() as $task) {
            foreach ($task->data as $index => $file) {
                try {
                    \Yii::$app->cloudStorage->upload($file);
                    $task->resolve($index);
                } catch (\Exception $e) {
                    \Yii::warning("failed to upload file '$file'", 'cnd');
                }
            }
            $task->save();
        }
    }

    public function actionDelete()
    {
        /** @var $task Task */
        foreach (Task::find()->deleteFromCDN()->each() as $task) {
            foreach ($task->data as $index => $file) {
                try {
                    \Yii::$app->cloudStorage->delete($file);
                    $task->resolve($index);
                } catch (\Exception $e) {
                    \Yii::warning("failed to delete file '$file'", 'cnd');
                }
            }
            $task->save();
        }
    }

    public function actionDeleteAll()
    {
        if ($this->confirm('Are you sure to delete all files?')) {
            \Yii::$app->cloudStorage->deleteAll();
        }
    }
}
