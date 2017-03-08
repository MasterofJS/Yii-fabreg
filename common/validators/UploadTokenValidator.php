<?php
/**
 * Created by PhpStorm.
 * User: Buba Suma
 * Date: 3/13/16
 * Time: 5:28 PM
 */

namespace common\validators;


use yii\base\InvalidConfigException;
use yii\validators\Validator;

class UploadTokenValidator extends Validator
{
    public $scenario;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (null === $this->scenario) {
            throw new InvalidConfigException('scenario property must be set');
        }
        if ($this->message === null) {
            $this->message = 'Upload token for {attribute} is invalid or expired';
        }
    }

    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;
        $result = $this->validateValue($value);
        if (!empty($result)) {
            $this->addError($model, $attribute, $result[0], $result[1]);
        }
    }

    /**
     * @inheritdoc
     */
    protected function validateValue($value)
    {
        if(!strncmp($value, 'default:', 8)){
            return null;
        }
        $session = \Yii::$app->session;
        $session->open();
        $info = $session->get($value);
        $session->close();
        if($info && !strcmp($info['scenario'], $this->scenario)){
            return null;
        }
        return [$this->message, []];
    }
}