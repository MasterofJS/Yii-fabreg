<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 2/26/16
 * Time: 10:30 AM
 */

namespace common\models;


use yii\base\Model;

class CommentForm extends Model
{
    public $post_id;
    public $reply_id;
    public $content;
    public $type;

    public function beforeValidate()
    {
        if(!empty($this->post_id)){
            $this->post_id = Post::decodeId($this->post_id);
        }
        return parent::beforeValidate();
    }


    public function rules()
    {
        return [
            [['post_id', 'content'], 'required'],
            [['reply_id', 'type'], 'integer'],
            ['post_id', 'exist',
                'targetClass' => Post::className(),
                'targetAttribute' => 'id'
            ],
            ['reply_id', 'exist',
                'targetClass' => Comment::className(),
                'targetAttribute' => ['reply_id' => 'id', 'post_id']
            ],
            ['type', 'in', 'range' => [Comment::TYPE_TEXT, Comment::TYPE_MEDIA]],
            ['content', 'filter',
                'filter' => function($value){
                    return trim(strip_tags($value));
                }
            ],
            ['content', 'string', 'max' => 500],
        ];
    }

    /**
     * @return null|Comment
     * @throws \Exception
     */
    public function comment()
    {
        try {
            $comment = $this->getComment();
            $comment->setAttributes($this->getAttributes());
            if (!$comment->save()) {
                $comment->throwValidationException();
            }
            return $comment;
        } catch (\Exception $ex) {
            if(!YII_DEBUG){
                \Yii::error($ex->getMessage(), __METHOD__);
                return null;
            }else{
                throw $ex;
            }
        }
    }

    public function getComment()
    {
        return new Comment();
    }

}