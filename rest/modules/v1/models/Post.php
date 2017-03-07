<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 2/25/16
 * Time: 5:17 PM
 */

namespace rest\modules\v1\models;


class Post extends \common\models\Post
{
    public function getAuthor()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @inheritdoc
     */
    public static function find()
    {
        return parent::find()->andWhere([static::tableName() . '.status' => self::STATUS_ACTIVE]);
    }


    public function fields()
    {
        return [
            'id' => function ($model) {
                return static::encodeId($model->id);
            },
            'type' => function ($model) {
                return $model->photo->getType();
            },
            'description',
            'description2' => function ($model) {
        			return $model->showDesc ? $model->description2 : null;
						},
						'showDesc',
            'is_nsfw',
            'timestamp' => function ($model) {
                return strtotime($model->created_at);
            },
            'comments',
            'likes',
            'dislikes',
            'liked',
            'disliked',
            'can_viewer_report' => 'canViewerReport',
            'can_viewer_delete' => 'canViewerDelete',
        ];
    }
}