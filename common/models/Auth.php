<?php

namespace common\models;

use common\components\UploadedFile;
use console\migrations\Migration;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\Json;
use yii\validators\UrlValidator;

/**
 * This is the model class for table "auth".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $source
 * @property string $source_id
 *
 * @property User $user
 *
 * @see m160224_130000_auth_table
 */
class Auth extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Migration::TABLE_USER_AUTH;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'source', 'source_id'], 'required'],
            [['user_id'], 'integer'],
            ['source_id', 'match', 'pattern' => '/^[a-z0-9A-Z_]+$/'],
            ['source', 'in', 'range' => ['google', 'facebook']],
            [['created_at', 'updated_at'], 'safe'],
            [['source', 'source_id'], 'string', 'max' => 255]
        ];
    }


    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @param $client \yii\authclient\ClientInterface
     * @return array
     */
    public static function parseAttributes($client)
    {
        $attributes = $client->getUserAttributes();
        $attributes['source'] = $client->getId();
        $attributes['source_id'] = $attributes['id'];
        $attributes['email'] = isset($attributes['email']) ? $attributes['email'] : null;
        $attributes['first_name'] = isset($attributes['first_name']) ? $attributes['first_name'] : null;
        $attributes['last_name'] = isset($attributes['last_name']) ? $attributes['last_name'] : null;
        $attributes['gender'] = isset($attributes['gender']) ? static::parseGender($attributes['gender']) : null;
        switch ($client->getId()) {
            case 'facebook':
                if (isset($attributes['source_id'])) {
                    $attributes['picture'] = Json::decode(
                        file_get_contents(
                            "http://graph.facebook.com/{$attributes['source_id']}/picture?type=large&redirect=false"
                        )
                    );
                    if (isset($attributes['picture']['data'])) {
                        if (!isset($attributes['picture']['data']['is_silhouette'])
                            || false == $attributes['picture']['data']['is_silhouette']) {
                            $attributes['avatar'] = $attributes['picture']['data']['url'];
                        }
                    }
                }
                break;
            case 'twitter':
                if (isset($attributes['name']) && !isset($attributes['first_name'], $attributes['last_name'])) {
                    $attributes['name'] = explode(' ', $attributes['name']);
                    if (count($attributes['name']) > 1) {
                        $attributes['last_name'] = end($attributes['name']);
                        array_pop($attributes['name']);
                        $attributes['first_name'] = implode(' ', $attributes['name']);
                    } else {
                        $attributes['first_name'] = current($attributes['name']);
                        $attributes['last_name'] = null;
                    }
                }
                break;
            case 'google':
                if (isset($attributes['emails'], $attributes['emails'][0]) && !isset($attributes['email'])) {
                    $attributes['email'] = isset($attributes['emails'][0]['value']) ?
                        $attributes['emails'][0]['value'] : null;
                }

                if (isset($attributes['displayName']) && !isset($attributes['first_name'], $attributes['last_name'])) {
                    $attributes['displayName'] = explode(' ', $attributes['displayName']);
                    if (count($attributes['displayName']) > 1) {
                        $attributes['last_name'] = end($attributes['displayName']);
                        array_pop($attributes['displayName']);
                        $attributes['first_name'] = implode(' ', $attributes['displayName']);
                    } else {
                        $attributes['first_name'] = current($attributes['displayName']);
                        $attributes['last_name'] = null;
                    }
                }

                if (isset($attributes['image'], $attributes['image']['url'])) {
                    $attributes['image']['url'] = preg_replace('/sz=(\d+)/i', 'sz=200', $attributes['image']['url']);
                    if (!isset($attributes['image']['isDefault']) || false == $attributes['image']['isDefault']) {
                        $attributes['avatar'] = $attributes['image']['url'];
                    }
                }
                break;
        }
        $attributes['password'] = '123' . Yii::$app->security->generateRandomString(6) . 'qwe';
        return $attributes;
    }

    /**
     * @param string $str
     * @return string|null
     */
    public static function parseGender($str)
    {
        if (is_string($str) && !empty($str)) {
            return strtoupper(substr($str, 0, 1));
        } else {
            return null;
        }
    }

    public static function find()
    {
        return new AuthQuery(get_called_class());
    }

    public static function upload($url)
    {
        $model = new ImageUploadForm(['scenario' => 'avatar']);
        if (null !== $url && (new UrlValidator())->validate($url)) {
            $model->image = UploadedFile::getInstanceByUrl($url);
        }
        $result = ImageUploadForm::upload($model);
        if (isset($result['error'])) {
            return null;
        }
        return $result['secureName'];
    }
}

class AuthQuery extends ActiveQuery
{

    public function facebook()
    {
        return $this->andWhere(['source' => 'facebook']);
    }

    /**
     * @param null $db
     * @return array|Auth
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
