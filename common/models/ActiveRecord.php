<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 2/23/16
 * Time: 5:25 PM
 */

namespace common\models;


use Hashids\Hashids;
use yii\base\Arrayable;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Link;
use yii\web\Linkable;

/**
 * Class ActiveRecord
 * @package common\models
 *
 * @property string $created_at
 * @property string $updated_at
 */
class ActiveRecord extends \yii\db\ActiveRecord
{
    const DATETIME_FORMAT = "Y-m-d H:i:s";
    const DATE_FORMAT = "Y-m-d";

    protected static $publicIdSalt = 'youshouldchangethissaltforeachmodel';
    protected static $publicIdMinLength = 6;
    protected static $publicIdSymbols = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    /**
     * @var Hashids[]
     */
    private static $_hashes = [];

    /**
     * @param int $id
     * @return string
     */
    public static function encodeId($id){
        if(empty(self::$_hashes[get_called_class()])){
            self::$_hashes[get_called_class()] = new Hashids( static::$publicIdSalt, static::$publicIdMinLength, static::$publicIdSymbols);
        }
        $result =  self::$_hashes[get_called_class()]->encode($id);
        if(!empty($result)){
            return $result;
        }
        return $id;
    }

    public static function decodeId($id){
        if(empty(self::$_hashes[get_called_class()])){
            self::$_hashes[get_called_class()] = new Hashids( static::$publicIdSalt, static::$publicIdMinLength, static::$publicIdSymbols);
        }
        try{
            $result =  self::$_hashes[get_called_class()]->decode($id);
            if(!empty($result)){
                return $result[0];
            }
        }catch (\Exception $ex){
            \Yii::error("cannot decode public id({$id})", __METHOD__);
        }
        return $id;
    }

    public function beforeSave($insert)
    {
        if($insert){
            $this->created_at = self::createDateTime();
        }
        $this->updated_at = self::createDateTime();

        return parent::beforeSave($insert);
    }


    public function events()
    {
        return [];
    }

    public function init()
    {
        parent::init();
        foreach ($this->events() as $event => $handlers) {
            foreach($handlers as $handler){
                if(ArrayHelper::isAssociative($handler)){
                    $this->on($event, $handler['handler'], ArrayHelper::getValue($handler, 'data'));
                }else{
                    $this->on($event, is_string($handler) ? [$this, $handler] : $handler);
                }
            }
        }
    }

    public function throwValidationException()
    {
        throw new \Exception(get_class($this) . ' : ' . Json::encode($this->errors));
    }

    public static function createDate($timestamp = null)
    {
        return date(self::DATE_FORMAT,$timestamp === null ? time():$timestamp);
    }

    public static function createDateTime($timestamp = null)
    {
        return date(self::DATETIME_FORMAT,$timestamp === null ? time():$timestamp);
    }

    public function toArray(array $fields = [], array $expand = [], $recursive = true)
    {
        $data = [];
        foreach ($this->resolveFields($fields, $expand) as $field => $definition) {
            $data[$field] = is_string($definition) ? $this->$definition : call_user_func($definition, $this, $field);
        }

        if ($this instanceof Linkable) {
            $data['_links'] = Link::serialize($this->getLinks());
        }

        return $recursive ? static::convertToArray($data, $fields, $expand) : $data;
    }

    protected static function convertToArray($object, array $fields = [], array $expand = [], $recursive = true)
    {
        if (is_array($object)) {
            if ($recursive) {
                foreach ($object as $key => $value) {
                    if (is_array($value) || is_object($value)) {
                        $object[$key] = static::convertToArray($value, $fields, $expand, true);
                    }
                }
            }
            return $object;
        } elseif (is_object($object)) {
            if ($object instanceof Arrayable) {
                $result = $object->toArray($fields, $expand, $recursive);
            } else {
                $result = [];
                foreach ($object as $key => $value) {
                    $result[$key] = $value;
                }
            }
            return $recursive ? static::convertToArray($result, $fields, $expand, true) : $result;
        } else {
            return [$object];
        }
    }
}