<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 2/25/16
 * Time: 5:40 PM
 */

namespace rest\modules\v1\controllers;

use yii\filters\Cors;

class Controller extends \yii\rest\Controller
{
    /**
     * @var string|array the configuration for creating the serializer that formats the response data.
     */
    public $serializer = 'rest\modules\v1\components\Serializer';

    public function behaviors()
    {
        return [
            'cors' => [
                'class' => Cors::className(),
            ],
        ];
    }
}
