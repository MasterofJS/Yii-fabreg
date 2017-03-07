<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 3/1/16
 * Time: 5:16 PM
 */

namespace rest\modules\v1\models;


class Auth extends \common\models\Auth
{
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public static function parseAttributes($client)
    {
        $attributes = parent::parseAttributes($client);
        if (!empty($attributes['password'])) {
            $attributes['confirm_password'] = $attributes['password'];
        }
        return $attributes;
    }


}