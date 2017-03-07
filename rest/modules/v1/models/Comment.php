<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 2/26/16
 * Time: 10:25 AM
 */

namespace rest\modules\v1\models;


class Comment extends \common\models\Comment
{
    public function fields()
    {
        return [
            'id',
            'content',
            'type',
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

    public function getAuthor()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getRepliedComment()
    {
        return $this->hasOne(static::className(), ['id' => 'reply_id']);
    }

    public function getPost()
    {
        return $this->hasOne(Post::className(), ['id' => 'post_id']);
    }

}