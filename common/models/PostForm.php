<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 2/25/16
 * Time: 4:59 PM
 */

namespace common\models;


use common\validators\UploadTokenValidator;
use yii\base\Model;

class PostForm extends Model
{
    public $description;
    public $description2;
    public $photo;
    public $is_nsfw;

    protected $_post = false;

    public function rules()
    {
        return [
            [['photo', 'description'], 'required'],
            ['is_nsfw', 'boolean'],
            [['description2'], 'string'],
            [['description', 'photo'], 'string', 'max' => 255],
            ['is_nsfw', 'default', 'value' => false],
            ['photo', UploadTokenValidator::className(), 'scenario' => 'post']
        ];
    }

    /**
     * @return null|Post
     * @throws \Exception
     */
    public function post()
    {
        $tr = \Yii::$app->db->beginTransaction();
        try {
            $post = $this->getPost();
            $post->setAttributes($this->getAttributes());
            $post->setPhoto($this->photo);
            if (!$post->save()) {
                $post->throwValidationException();
            }
            $tr->commit();
            return $post;
        } catch (\Exception $ex) {
            $tr->rollBack();
            if(!YII_DEBUG){
                \Yii::error($ex->getMessage(), __METHOD__);
                return null;
            }else{
                throw $ex;
            }
        }
    }

    public function getPost()
    {
        return new Post();
    }


}