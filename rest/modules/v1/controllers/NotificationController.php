<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 3/4/16
 * Time: 11:10 AM
 */

namespace rest\modules\v1\controllers;


use rest\modules\v1\models\Notification;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class NotificationController extends Controller
{
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ]);
    }

    public function actionIndex()
    {
        return new ActiveDataProvider([
            'query' => Notification::find()
                ->with([
                    'actor',
                    'lastActor',
                ])
                ->andWhere(['receiver_id' => \Yii::$app->user->id]),
            'pagination' => [
                'pageSizeLimit' => [5, 20],
            ],
            'sort' => [
                'defaultOrder' => [
                    'updated_at' => SORT_DESC
                ]
            ]
        ]);
    }

    public function actionReadAll()
    {
        Notification::updateAll(['is_read' => true], ['receiver_id' => \Yii::$app->user->id, 'is_read' => false]);
        \Yii::$app->getResponse()->setStatusCode(204);
    }

    public function actionGetUnread()
    {
        return Notification::find()->andWhere(['receiver_id' => \Yii::$app->user->id, 'is_read' => false])->count();
    }
}