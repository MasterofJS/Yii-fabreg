<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 3/4/16
 * Time: 11:15 AM
 */

namespace rest\modules\v1\models;

/**
 * Class Notification
 * @package rest\modules\v1\models
 *
 * @property User actor
 * @property User lastActor
 */
class Notification extends \common\models\Notification
{
    private $_entity;

    public function getActor()
    {
        return $this->hasOne(User::className(), ['id' => 'actor_id']);
    }

    public function getLastActor()
    {
        return $this->hasOne(User::className(), ['id' => 'last_actor_id']);
    }

    public function fields()
    {
        $fields = [
            'type' => function ($model) {
                if (self::TYPE_COMMENT == $model['type'] && Post::TYPE == $model['entity_type']) {
                    return 'comment_post';
                } elseif (self::TYPE_LIKE == $model['type'] && Post::TYPE == $model['entity_type']) {
                    return 'like_post';
                } elseif (self::TYPE_SHARE == $model['type'] && Post::TYPE == $model['entity_type']) {
                    return 'share_post';
                } elseif (self::TYPE_COMMENT == $model['type'] && Comment::TYPE == $model['entity_type']) {
                    return 'reply_comment';
                } elseif (self::TYPE_LIKE == $model['type'] && Comment::TYPE == $model['entity_type']) {
                    return 'like_comment';
                } elseif (self::TYPE_TRENDING == $model['type'] && Post::TYPE == $model['entity_type']) {
                    return 'trending_post';
                } elseif (self::TYPE_HOT == $model['type'] && Post::TYPE == $model['entity_type']) {
                    return 'hot_post';
                } else {
                    return 'unknown';
                }
            },
            'is_read',
            'count' => function ($model) {
                return intval($model['last_count']) && !empty($model['last_actor_id']) ? intval($model['last_count']) - 1 : intval($model['last_count']);
            },
            'timestamp' => function ($model) {
                return strtotime($model['updated_at']);
            },
            'post' => function ($model) {
                switch ($model['entity_type']) {
                    case Post::TYPE:
                        return $model['entity'];
                    case Comment::TYPE:
                        return $model['entity']['post'];
                    default:
                        return null;
                }
            }
        ];
        return $fields;
    }

    public function extraFields()
    {
        $fields = parent::extraFields();
        $fields['actors'] = function ($model) {
            $models = [];
            if (!empty($model['actor'])) {
                $models[] = $model['actor'];
            }
            if (!empty($model['lastActor'])) {
                $models[] = $model['lastActor'];
            }
            return $models;
        };
        return $fields;
    }

    public function getEntity()
    {
        if (null === $this->_entity) {
            switch ($this->entity_type) {
                case Post::TYPE:
                    $this->_entity = Post::findOne(['id' => $this->entity_id]);
                    $this->_entity->isNotification = 1;
                    break;
                case Comment::TYPE:
                    $this->_entity = Comment::findOne(['id' => $this->entity_id]);
                    $this->_entity->post->isNotification = 1;
                    break;
                default:
                    $this->_entity = null;
            }
        }
        return $this->_entity;
    }
}