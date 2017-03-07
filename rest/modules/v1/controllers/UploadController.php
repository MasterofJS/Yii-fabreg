<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 2/24/16
 * Time: 3:42 PM
 */

namespace rest\modules\v1\controllers;


use common\components\UploadedFile;

;
use common\models\ImageUploadForm;

use Yii;
use yii\validators\UrlValidator;

class UploadController extends Controller
{
    public function actionPhoto()
    {
        $request = Yii::$app->request;
        $response = [];
        $scenario = $request->post('scenario', $request->get('scenario'));
        $model = new ImageUploadForm(['scenario' => $scenario]);
        $model->image = UploadedFile::getInstanceByName('photo');

        if (!$model->image) {
            $url = $request->getBodyParam('photo');
            if (null !== $url && (new UrlValidator())->validate($url)) {
                $model->image = UploadedFile::getInstanceByUrl($url);
            }
        }
        $preview = $request->post('preview', $request->get('preview'));
        if ($file = ImageUploadForm::upload($model, $preview)) {
            $response['files'][] = $file;
        }
        return $response;
    }
}