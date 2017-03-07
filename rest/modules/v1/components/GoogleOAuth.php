<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 3/1/16
 * Time: 3:23 PM
 */

namespace rest\modules\v1\components;

class GoogleOAuth extends \yii\authclient\clients\GoogleOAuth
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        if (empty($this->clientId)) {
            $this->clientId = \Yii::$app->get('variables')->get('social', 'google.client_id');
        }
        if (empty($this->clientSecret)) {
            $this->clientSecret = \Yii::$app->get('variables')->get('social', 'google.client_secret');
        }
    }
}
