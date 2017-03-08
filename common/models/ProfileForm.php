<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 2/26/16
 * Time: 5:27 PM
 */

namespace common\models;




use common\validators\UploadTokenValidator;
use yii\helpers\ArrayHelper;

class ProfileForm  extends UserForm
{
    public $notify_post_upvote;
    public $notify_post_comment;
    public $notify_post_share;
    public $notify_comment_upvote;
    public $notify_comment_reply;

    public function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => ['first_name', 'last_name', 'gender', 'country', 'birthday', 'about', 'avatar', 'notify_post_upvote', 'notify_post_comment', 'notify_post_share', 'notify_comment_upvote', 'notify_comment_reply'],
            'media' => ['avatar', 'cover'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge( parent::rules(),[

            [['first_name', 'last_name'], 'required'],
            [['notify_post_upvote', 'notify_post_comment', 'notify_post_share', 'notify_comment_upvote', 'notify_comment_reply'], 'boolean'],
            ['avatar', UploadTokenValidator::className(), 'scenario' => 'avatar'],
            ['cover', UploadTokenValidator::className(), 'scenario' => 'cover'],
        ] );
    }


    /**
     * @return User
     */
    public function getUser()
    {
        return \Yii::$app->user->identity;
    }

    public function save()
    {
        $tr = \Yii::$app->db->beginTransaction();
        try {
            $user = $this->getUser();
            $user->setAttributes($this->getAttributes($this->safeAttributes()));
            $user->setAvatar($this->avatar);
            $user->setCover($this->cover);
            if (!$user->save()) {
                $user->throwValidationException();
            }
            $tr->commit();
            return $user;

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

}