<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 4/26/16
 * Time: 6:23 PM
 */

namespace common\components;


class EmailTarget extends \yii\log\EmailTarget
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        if (empty($this->message['to'])) {
            $this->message['to'] = \Yii::$app->get('variables')->get('email', 'support');
        }
        if (empty($this->message['from'])) {
            $this->message['from'] = \Yii::$app->get('variables')->get('email', 'noreply');
        }
        parent::init();
    }
}