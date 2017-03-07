<?php
/**
 * Created by PhpStorm.
 * User: buba
 * Date: 12/2/15
 * Time: 1:02 PM
 */

namespace backend\models;

use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\validators\Validator;

class Variable extends \common\models\Variable
{

    public function __construct($config = [])
    {
        $this->setScenario('update');
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['key', 'namespace', 'type', 'value'], 'trim'],
            [['key', 'namespace', 'type', 'value'], 'safe', 'on' => 'search'],
            [['key', 'namespace', 'type', 'value'], 'required', 'on' => ['create', 'update']],
            ['key', 'unique', 'on' => ['create', 'update'],
                'targetAttribute' => ['namespace', 'key'],
                'message' => '{attribute} "{value}" already exists in the given namespace.'],
            ['value', 'valueValidator', 'on' => ['create', 'update']],
            ['key', 'string', 'max' => 30, 'on' => ['create', 'update']],
            ['namespace', 'string', 'max' => 30, 'on' => ['create', 'update']],
            ['namespace', 'compare', 'operator' => '!=', 'compareValue' => 'system', 'on' => ['create', 'update']],
        ];
    }

    public function valueValidator()
    {
        if (!empty($this->value) && !$this->hasErrors($this->type)) {
            $rule = $this->getRules()[$this->type];
            $validator = Validator::createValidator($rule[0], $this, 'value', array_slice($rule, 1));
            $validator->validateAttribute($this, 'value');
        }
    }


    public function search($params)
    {
        $this->load($params);
        $query = static::find();
        $query->andFilterWhere(['type' => $this->type]);
        $query->andFilterWhere([
            'or like',
            'namespace',
            preg_split('/\P{L}+/u', $this->namespace, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE)
        ]);
        $query->andFilterWhere([
            'or like',
            'key',
            preg_split('/\P{L}+/u', $this->key, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE)
        ]);
        $query->andFilterWhere([
            'or like',
            'value',
            preg_split('/\P{L}+/u', $this->value, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE)
        ]);

        return new ActiveDataProvider(['query' => $query, 'pagination' => false]);
    }

    public static function getTypes()
    {
        return [
            self::TYPE_STRING => 'String',
            self::TYPE_EMAIL => 'Email',
            self::TYPE_INTEGER => 'Integer',
            self::TYPE_NUMBER => 'Number',
            self::TYPE_DATE => 'Date',
            self::TYPE_DATE_TIME => 'Datetime',
            self::TYPE_TIME => 'Time',
            self::TYPE_HOUR => 'Hour',
            self::TYPE_BOOLEAN => 'Boolean',
            self::TYPE_URL => 'Url',
            self::TYPE_DIRECTORY => 'Directory',
            self::TYPE_FILE => 'File',
            self::TYPE_CURRENCY => 'Currency',
            self::TYPE_EXCHANGE_RATE_SOURCE => 'Exchange rate source',
            self::TYPE_PERCENTAGE => 'Percentage',
            self::TYPE_MINUTE => 'Minute',
            self::TYPE_SECOND => 'Second',
        ];
    }

    public function getRules()
    {
        return [
            self::TYPE_STRING => ['string', 'max' => 255],
            self::TYPE_EMAIL => ['email'],
            self::TYPE_INTEGER => ['integer', 'integerPattern' => '/^\s*\d+\s*$/'],
            self::TYPE_HOUR => ['integer', 'integerPattern' => '/^\s*\d+\s*$/'],
            self::TYPE_MINUTE => ['integer', 'integerPattern' => '/^\s*\d+\s*$/'],
            self::TYPE_SECOND => ['integer', 'integerPattern' => '/^\s*\d+\s*$/'],
            self::TYPE_NUMBER => ['number', 'numberPattern' => '/^\s*[0-9]*\.?[0-9]+\s*$/'],
            self::TYPE_DATE => ['date', 'format' => 'php:Y-m-d'],
            self::TYPE_DATE_TIME => ['date', 'format' => 'php:Y-m-d H:i:s'],
            self::TYPE_TIME => ['date', 'format' => 'php:H:i:s'],
            self::TYPE_BOOLEAN => ['boolean', 'strict' => true, 'trueValue' => 'true', 'falseValue' => false],
            self::TYPE_URL => ['url'],
            self::TYPE_DIRECTORY => ['\common\validators\DirectoryValidator'],
            self::TYPE_FILE => ['\common\validators\FileValidator'],
            self::TYPE_CURRENCY => ['exist', 'targetAttribute' => 'code', 'targetClass' => '\common\models\Currency'],
            self::TYPE_EXCHANGE_RATE_SOURCE => ['in', 'range' => ['yahoo', 'currencyLayer', 'open'],],
            self::TYPE_PERCENTAGE => ['in', 'range' => range(0, 100),],
        ];
    }

    public function getType()
    {
        $array = self::getTypes();
        return isset($array[$this->type]) ? $array[$this->type] : $this->type;
    }

    public static function getCountNewVariables($variables)
    {
        static::getNewVariables($variables, $count);
        return $count;
    }

    public static function getNewVariables($variables, &$count = null)
    {
        $newVariables = [];
        $count = 0;
        $databaseVariables = self::find()->asArray()->indexBy('key')->select('key')->column();
        foreach ($variables as $namespace => $keys) {
            foreach ($keys as $key => $var) {
                if (!in_array($key, $databaseVariables)) {
                    $newVariables[$namespace][$key] = $var;
                    $count++;
                }
            }
        }
        return $newVariables;
    }

    /**
     * @return ActiveQuery
     */
    public static function find()
    {
        return parent::find()->andWhere(['<>','namespace', 'system']);
    }
}
