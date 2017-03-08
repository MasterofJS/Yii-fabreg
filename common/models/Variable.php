<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 3/14/16
 * Time: 12:03 PM
 */

namespace common\models;

use console\migrations\Migration;
use yii\helpers\ArrayHelper;


/**
 * Class Variables
 *
 * @package common\models
 * @property string id
 * @property string key
 * @property string namespace
 * @property string type
 * @property string value
 *
 * @see m160314_100503_variable_table
 */

class Variable extends ActiveRecord
{
    const TYPE_STRING = 'string';
    const TYPE_EMAIL = 'email';
    const TYPE_INTEGER = 'integer';
    const TYPE_NUMBER = 'number';
    const TYPE_DATE = 'date';
    const TYPE_DATE_TIME = 'datetime';
    const TYPE_TIME = 'time';
    const TYPE_HOUR = 'hour';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_URL = 'url';
    const TYPE_DIRECTORY = 'directory';
    const TYPE_FILE = 'file';
    const TYPE_CURRENCY = 'currency';
    const TYPE_EXCHANGE_RATE_SOURCE = 'exch.rate.src';
    const TYPE_PERCENTAGE = 'percentage';
    const TYPE_MINUTE = 'minute';
    const TYPE_SECOND = 'second';

    public $path = '@common/data/variables.php';

    private $_variables = [];
    private $_defaults;

    public function get($namespace, $key, &$type = null)
    {
        if (!isset($this->_variables[$namespace])) {
            $schema = static::getDb()->getSchema()->getTableSchema(static::tableName());
            if ($schema) {
                $this->_variables[$namespace] = static::find()
                    ->where(['namespace' => $namespace])
                    ->select(['key', 'value', 'type'])
                    ->indexBy('key')
                    ->all();
            } else {
                $this->_variables[$namespace] = [];
            }
        }
        if (!isset($this->_variables[$namespace][$key])) {
            if (null === $this->_defaults) {
                $file = \Yii::getAlias($this->path);
                if (is_file($file)) {
                    $this->_defaults = require($file);
                }
                if (!is_array($this->_defaults)) {
                    $this->_defaults = [];
                }
            }

            if (!isset($this->_defaults[$namespace][$key])) {
                return null;
            }
            $type = $this->_defaults[$namespace][$key]['type'];
            return $this->_defaults[$namespace][$key]['value'];
        }
        $type = $this->_variables[$namespace][$key]['type'];
        return $this->_variables[$namespace][$key]['value'];
    }

    public static function toSeconds($value, $type)
    {
        $seconds = intval($value);
        if (Variable::TYPE_HOUR == $type) {
            $seconds *= 3600;
        } elseif (Variable::TYPE_MINUTE == $type) {
            $seconds *= 60;
        }
        return $seconds;
    }

    public static function tableName()
    {
        return Migration::TABLE_VARIABLE;
    }
}
