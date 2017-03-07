<?php

namespace rest\modules\v1\controllers;

use rest\modules\v1\models\Avatar;
use yii\data\ActiveDataProvider;

class MediaController extends Controller
{
    public function actionDefaultAvatars()
    {
        return new ActiveDataProvider([
            'query' => Avatar::find()->defaults(),
        ]);
    }
}