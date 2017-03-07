<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 3/1/16
 * Time: 12:57 PM
 */

namespace rest\modules\v1\components;

class Facebook extends \yii\authclient\clients\Facebook
{
    public $attributeNames = ['id', 'first_name', 'last_name', 'gender', 'email'];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        if (empty($this->clientId)) {
            $this->clientId = \Yii::$app->get('variables')->get('social', 'facebook.app_id');
        }
        if (empty($this->clientSecret)) {
            $this->clientSecret = \Yii::$app->get('variables')->get('social', 'facebook.app_secret');
        }
    }
}
