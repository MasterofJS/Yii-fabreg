<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 4/22/16
 * Time: 12:42 PM
 */

namespace console\controllers;


use common\helpers\FileHelper;
use common\models\Notification;
use common\models\Variable;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class CleanerController extends Controller
{
    /**
     * @var string $tempFolder
     * @see http://php.net/manual/en/datetime.formats.relative.php
     * @return int
     */
    public function actionTmp($tempFolder = '@frontend/web/tmp')
    {
        $dir = Yii::getAlias($tempFolder);
        if (!is_dir($dir)) {
            $this->stdout("The 'tempFolder' param must refer to a valid directory.\n", Console::FG_RED);
            return self::EXIT_CODE_ERROR;
        }
        $now = (new \DateTime())->getTimestamp();
        $type = null;
        $relativeTime = \Yii::$app->get('variables')->get('cleaner', 'temp.files', $type);
        if (Variable::TYPE_MINUTE === $type) {
            $relativeTime *= 60;
        } elseif (Variable::TYPE_HOUR === $type) {
            $relativeTime *= 3600;
        }
        foreach (FileHelper::findFiles($dir) as $filename) {
            if (file_exists($filename)) {
                $fileModificationTime = filemtime($filename);
                $temp = date_add(date_create("@$fileModificationTime"), new \DateInterval("PT{$relativeTime}S"));
                $temp = $temp->getTimestamp();
                if ($now >= $temp) {
                    $this->stdout("deleting file ", Console::FG_RED);
                    $this->stdout($filename . "\n");
                    unlink($filename);
                }
            }
        }
        $this->stdout("temporary folder is already clean!.\n", Console::FG_GREEN);
        return self::EXIT_CODE_NORMAL;
    }

    public function actionAssets($assetsFolder = '@frontend/web/assets')
    {
        $dir = Yii::getAlias($assetsFolder);
        if (!is_dir($dir)) {
            $this->stdout("The 'assetsFolder' param must refer to a valid directory.\n", Console::FG_RED);
            return self::EXIT_CODE_ERROR;
        }
        foreach (FileHelper::findFiles($dir) as $filename) {
            $this->stdout("deleting asset folder ", Console::FG_RED);
            $this->stdout($filename . "\n");
            FileHelper::removeDirectory($filename);
        }
        $this->stdout("assets folder is already clean!.\n", Console::FG_GREEN);
        return self::EXIT_CODE_NORMAL;
    }

    public function actionNotifications()
    {
        $type = null;
        $relativeTime = \Yii::$app->get('variables')->get('cleaner', 'notifications', $type);
        if (Variable::TYPE_MINUTE === $type) {
            $relativeTime *= 60;
        } elseif (Variable::TYPE_HOUR === $type) {
            $relativeTime *= 3600;
        }
        $date = new \DateTime();
        $date->sub(new \DateInterval("PT{$relativeTime}S"));
        Notification::deleteAll(['<=', 'updated_at', $date->format(Notification::DATETIME_FORMAT)]);
    }

}