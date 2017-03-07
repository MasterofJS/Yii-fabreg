<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 2/25/16
 * Time: 6:19 PM
 */

namespace rest\modules\v1\components;

use yii\base\Model;

class Serializer extends \yii\rest\Serializer
{
    /**
     * Serializes the validation errors in a model.
     * @param Model $model
     * @return array the array representation of the errors
     */
    protected function serializeModelErrors($model)
    {
        $result = [];
        foreach ($model->getFirstErrors() as $name => $message) {
            $result[] = [
                'field' => $name,
                'message' => $message,
            ];
        }

        return $result;
    }
}
