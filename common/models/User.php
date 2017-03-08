<?php

namespace common\models;

use common\interfaces\EntityInterface;
use console\migrations\Migration;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\SluggableBehavior;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email_confirmation_token
 * @property string $email
 * @property integer $status
 * @property string $first_name
 * @property string $last_name
 * @property string $gender
 * @property string $country
 * @property string $about
 * @property string $deletion_reason
 * @property string $birthday
 * @property string $avatar_id
 * @property string $cover_id
 * @property bool $show_nswf
 * @property bool $hide_upvotes
 * @property bool $notify_post_upvote
 * @property bool $notify_post_comment
 * @property bool $notify_post_share
 * @property bool $notify_comment_upvote
 * @property bool $notify_comment_reply
 * @property string $api_usage
 * @property bool $canViewerMute
 * @property bool $canViewerUnmute
 * @property mixed $mute set-only
 * @property-read string $password
 * @property-read Avatar $avatar
 * @property-read Cover $cover
 *
 * @see m160224_120000_user_table
 */
class User extends ActiveRecord implements IdentityInterface
{

    const USERNAME_PATTERN = '/^\s*[a-z][0-9a-z-]+\s*$/';

    const GENDER_MALE = 'M';
    const GENDER_FEMALE = 'F';
    const GENDER_OTHER = 'O';

    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;
    const STATUS_BANNED = 5;

    const REL_AVATAR = 'avatar';
    const REL_COVER = 'cover';

    protected static $publicIdSalt = 'zmwjlhnucyinforg';

    private $_can_viewer_mute;
    private $_can_viewer_unmute;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Migration::TABLE_USER;
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            ['class' => SluggableBehavior::className(), 'slugAttribute' => 'username', 'attribute' => ['first_name', 'last_name'], 'ensureUnique' => true, 'immutable' => true],
        ];
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'email', 'first_name', 'last_name', 'gender'], 'required'],
            [['status', 'avatar_id'], 'integer'],
            [['created_at', 'updated_at'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            ['birthday', 'date', 'format' => 'php:Y-m-d'],
            [['username', 'password_hash', 'password_reset_token', 'email_confirmation_token', 'email'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['first_name', 'last_name'], 'string', 'max' => 31],
            [['gender'], 'string', 'max' => 1],
            [['country'], 'string', 'max' => 2],
            [['about'], 'string', 'max' => 1020],
            ['api_usage', 'string'],
            [['deletion_reason'], 'string', 'max' => 255],
            [['username'], 'unique'],
            ['email', 'email'],
            [['email'], 'unique', 'message' => 'Esse email já está sendo usado por outro usuário.'],
            [['password_reset_token'], 'unique'],
            [['notify_post_upvote', 'notify_post_comment', 'notify_post_share', 'notify_comment_upvote', 'notify_comment_reply', 'hide_upvotes', 'show_nswf'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return self|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => \rest\modules\v1\models\User::STATUS_ACTIVE
        ]);
    }

    /**
     * Finds user by email confirmation token token
     *
     * @param string $token password reset token
     * @return static
     */
    public static function findByEmailConfirmationToken($token)
    {
        if (!static::isEmailConfirmationTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'email_confirmation_token' => $token,
            'status' => \rest\modules\v1\models\User::STATUS_ACTIVE
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire = 3600 * \Yii::$app->get('variables')->get('token', 'password');
        return $timestamp + $expire >= time();
    }

    /**
     * Finds out if email confirmation token is valid
     *
     * @param string $token account email confirmation token
     * @return boolean
     */
    public static function isEmailConfirmationTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire = 3600 * \Yii::$app->get('variables')->get('token', 'email');
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }


    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * Generates new email confirmation  token
     */
    public function generateEmailConfirmationToken()
    {
        $this->email_confirmation_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes email confirmation token
     */
    public function removeEmailConfirmationToken()
    {
        $this->email_confirmation_token = null;
    }

    public function afterSave($insert, $changedAttributes)
    {
        if (array_key_exists('avatar_id', $changedAttributes)) {
            if (!empty($changedAttributes['avatar_id'])) {
                $avatar = Avatar::findById($changedAttributes['avatar_id']);
                if ($avatar && !$avatar->is_default) {
                    $avatar->delete();
                }
            }
            if (!empty($this->avatar_id) && !$this->avatar->is_default) {
                $avatar = $this->avatar;
                $tempFileName = $avatar->getBaseName();
                $avatar->name = static::encodeId($this->id) . '_a_' . time();
                $avatar->handle($tempFileName);
            }
        }

        if (array_key_exists('cover_id', $changedAttributes)) {
            if (!empty($changedAttributes['cover_id'])) {
                $cover = Cover::findById($changedAttributes['cover_id']);
                if ($cover && !$cover->is_default) {
                    $cover->delete();
                }
            }
            if (!empty($this->cover_id) && !$this->cover->is_default) {
                $cover = $this->cover;
                $tempFileName = $cover->getBaseName();
                $cover->name = static::encodeId($this->id) . '_c_' . time();
                $cover->handle($tempFileName);
            }
        }

        if (array_key_exists('status', $changedAttributes)) {
            if (self::STATUS_DELETED == $this->status) {
                Auth::deleteAll(['user_id' => $this->id]);
            }

            if (self::STATUS_BANNED == $this->status) {
                \Yii::$app
                    ->mailer
                    ->compose(
                        ['html' => 'user-ban'],
                        ['user' => $this]
                    )
                    ->setFrom([\Yii::$app->get('variables')->get('email', 'noreply') => \Yii::$app->name])
                    ->setTo($this->email)
                    ->setSubject('Sua conta foi apagada.')
                    ->send();

            }
        }

        if (empty($this->api_usage)) {
            $this->api_usage = [];
        } elseif (is_string($this->api_usage)) {
            $this->api_usage = Json::decode($this->api_usage);
        }

        parent::afterSave($insert, $changedAttributes);
    }


    /**
     * @param string $secureName
     */
    public function setAvatar($secureName)
    {
        if (!$secureName) {
            return;
        }
        if (0 === strncmp($secureName, 'default:', 8)) {
            $secureName = str_replace('default:', '', $secureName);
            $image = Avatar::findByName($secureName);
            if ($image) {
                $this->avatar_id = $image->id;
            }
        } else {
            $session = Yii::$app->session;
            $session->open();
            $imageInfo = $session->get($secureName);
            $session->close();
            if (null !== $imageInfo) {
                $image = new Avatar();
                $image->setAttributes($imageInfo);
                if (!$image->save()) {
                    $image->throwValidationException();
                }
                $this->avatar_id = $image->id;
            }
        }
    }

    /**
     * @param string $secureName
     */
    public function setCover($secureName)
    {
        if (!$secureName) {
            return;
        }
        $session = Yii::$app->session;
        $session->open();
        $imageInfo = $session->get($secureName);
        $session->close();
        if (null !== $imageInfo) {
            $image = new Cover();
            $image->setAttributes($imageInfo);
            if (!$image->save()) {
                $image->throwValidationException();
            }
            $this->cover_id = $image->id;
        }
    }

    public function getAvatar()
    {
        return $this->hasOne(Avatar::className(), ['id' => 'avatar_id']);
    }

    public function getCover()
    {
        return $this->hasOne(Cover::className(), ['id' => 'cover_id']);
    }

    public function isActive()
    {
        return self::STATUS_ACTIVE == $this->status;
    }

    public function isBanned()
    {
        return self::STATUS_BANNED == $this->status;
    }

    public function isDeleted()
    {
        return self::STATUS_DELETED == $this->status;
    }

    public function getViewUrl($scheme = true)
    {
        return Url::toRoute(['user/view', 'username' => $this->username], $scheme);
    }


    /**
     * @param string $userId
     * @return bool
     */
    public function hasTurnedOff($userId)
    {
        return Mute::find()->andWhere(['receiver_id' => $this->id, 'sender_id' => $userId])->exists();
    }

    /**
     * @param Notification $notification
     * @return bool
     */
    public function allowsNotification($notification)
    {
        if ($this->id == $notification->actor_id) {
            return false;
        }

        if (false == $this->notify_post_comment && Notification::TYPE_COMMENT == $notification->type && Post::TYPE == $notification->entity_type) {
            return false;
        }

        if (false == $this->notify_post_upvote && Notification::TYPE_LIKE == $notification->type && Post::TYPE == $notification->entity_type) {
            return false;
        }

        if (false == $this->notify_post_share && Notification::TYPE_SHARE == $notification->type && Post::TYPE == $notification->entity_type) {
            return false;
        }

        if (false == $this->notify_comment_reply && Notification::TYPE_COMMENT == $notification->type && Comment::TYPE == $notification->entity_type) {
            return false;
        }

        if (false == $this->notify_comment_upvote && Notification::TYPE_LIKE == $notification->type && Comment::TYPE == $notification->entity_type) {
            return false;
        }

        if ($this->hasTurnedOff($notification->actor_id)) {
            return false;
        }

        return true;
    }

    /**
     * @param EntityInterface $entity
     * @return bool
     */
    public function hasReported($entity)
    {
        switch ($entity->getType()) {
            case Post::TYPE:
                $exist = PostReport::find()->andWhere(['user_id' => $this->id, 'post_id' => $entity->getType()])->exists();
                break;
            case Comment::TYPE:
                $exist = CommentReport::find()->andWhere(['user_id' => $this->id, 'comment_id' => $entity->getType()])->exists();
                break;
            default:
                $exist = false;
        }
        return $exist;
    }

    /**
     * @return bool
     */
    public function getCanViewerMute()
    {
        if ($this->isNewRecord) {
            return null;
        }
        if ($this->_can_viewer_mute === null) {
            if (Yii::$app->user->isGuest || \Yii::$app->user->id == $this->id) {
                $this->setCanViewerMute(false);
            } else {
                $this->setCanViewerMute(!Mute::find()->andWhere(['receiver_id' => $this->id, 'sender_id' => Yii::$app->user->id])->exists());
            }
        }
        return $this->_can_viewer_mute;
    }

    /**
     * @param bool $can_viewer_mute
     */
    public function setCanViewerMute($can_viewer_mute)
    {
        $this->_can_viewer_mute = !!$can_viewer_mute;
    }

    /**
     * @return bool
     */
    public function getCanViewerUnmute()
    {
        if ($this->isNewRecord) {
            return null;
        }
        if ($this->_can_viewer_unmute === null) {
            if (Yii::$app->user->isGuest || \Yii::$app->user->id == $this->id) {
                $this->setCanViewerUnmute(false);
            } else {
                $this->setCanViewerUnmute(!$this->getCanViewerMute());
            }
        }
        return $this->_can_viewer_unmute;
    }

    /**
     * @param bool $can_viewer_unmute
     */
    public function setCanViewerUnmute($can_viewer_unmute)
    {
        $this->_can_viewer_unmute = $can_viewer_unmute;
    }

    /**
     * @param mixed $mute
     */
    public function setMute($mute)
    {
        $this->_can_viewer_mute = !$mute;
    }

    public function sendEmailConfirmationLink()
    {
        if (!static::isEmailConfirmationTokenValid($this->email_confirmation_token)) {
            $this->generateEmailConfirmationToken();
        }

        if (!$this->save()) {
            return false;
        }
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'confirm-email'],
                ['user' => $this]
            )
            ->setFrom([\Yii::$app->get('variables')->get('email', 'noreply') => \Yii::$app->name])
            ->setTo($this->email)
            ->setSubject('Confirme o seu e-mail no ' . \Yii::$app->name)
            ->send();
    }

    public function sendWelcome()
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'welcome'],
                ['user' => $this]
            )
            ->setFrom([\Yii::$app->get('variables')->get('email', 'noreply') => \Yii::$app->name])
            ->setTo($this->email)
            ->setSubject('Bem-vinda(o) à  ' . \Yii::$app->name)
            ->send();
    }

    public function getHasConfirmedEmail()
    {
        return empty($this->email_confirmation_token);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (self::STATUS_DELETED == $this->getOldAttribute('status') && self::STATUS_DELETED != $this->status) {
                $this->deletion_reason = null;
            }
            return true;
        }
        return false;
    }

    public function beforeValidate()
    {
        if (empty($this->avatar_id)) {
            $this->avatar_id = Avatar::random();
        }
        if (empty($this->api_usage)) {
            $this->api_usage = null;
        } else if (is_array($this->api_usage)) {
            $this->api_usage = Json::encode((array)$this->api_usage);
        }
        return parent::beforeValidate();
    }

    public function afterFind()
    {
        parent::afterFind();
        if (empty($this->api_usage)) {
            $this->api_usage = [];
        } elseif (is_string($this->api_usage)) {
            $this->api_usage = Json::decode($this->api_usage);
        }
    }

    public function getSocialLinks()
    {
        return $this->hasMany(Auth::className(), ['user_id' => 'id']);
    }
}
