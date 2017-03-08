<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 2/24/16
 * Time: 1:36 PM
 */

namespace common\models;


use common\validators\UploadTokenValidator;
use yii\behaviors\SluggableBehavior;
use yii\helpers\ArrayHelper;

/**
 * Class SignUpForm
 * @package common\models
 *
 * @property-read User $user
 */
class SignUpForm extends UserForm
{
    //auth fields
    public $source_id;
    public $source;
    public $confirm_password;

    public function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => ['username','email', 'first_name', 'last_name', 'gender', 'country', 'birthday', 'password', 'confirm_password', 'about', 'avatar', 'source', 'source_id'],
        ];
    }

    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => SluggableBehavior::className(),
                'attribute' => ['first_name', 'last_name'],
                'slugAttribute' => 'username',
                'ensureUnique' => true,
                'uniqueValidator' => ['targetClass' => '\common\models\User']
            ]
        ];
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'confirm_password' => 'Confirmar Senha'
        ]);
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge( parent::rules(),[

            [['email', 'first_name', 'last_name', 'password'], 'required'],

            [['source', 'source_id'], 'filter',
                'filter' => 'trim'
            ],

            ['source_id', 'match',
                'pattern'=>'/^[a-z0-9A-Z_]+$/'
            ],

            ['source',
                'in',
                'range' => ['google','facebook']
            ],

            [['source', 'source_id'], 'string',
                'max' => 255
            ],

            [['password', 'confirm_password'], 'required'],
            ['password', 'match', 'pattern' => '/(?=^.{6,20}$)(?=.*\d)(?=.*[A-Za-z]).*$/', 'message' => 'A senha deve conter um mínimo de 6 símbolos e máximo de 20. Contendo ao menos uma letra e um número.'],

            ['confirm_password', 'compare', 'compareAttribute'=>'password', 'operator' => '=='],

            ['email', 'unique',
                'targetClass' => '\common\models\User',
                'message' => 'Esse email já está em uso.',
            ],

            ['avatar', UploadTokenValidator::className(), 'scenario' => 'avatar'],

        ] );
    }

    /**
     * @return null|User
     * @throws \Exception
     * @throws \yii\db\Exception
     */
    public function signUp()
    {
        $tr = \Yii::$app->db->beginTransaction();
        try {
            $user = $this->getUser();
            $user->setAttributes($this->getAttributes($this->safeAttributes()));
            $user->setPassword($this->password);
            $user->setAvatar($this->avatar);
            $user->generateAuthKey();

            if (!($this->source_id && $this->source)) {
                $user->generateEmailConfirmationToken();
            }

            if (!$user->save()) {
                $user->throwValidationException();
            }
            if ($this->source_id && $this->source) {
                $auth = new Auth([
                    'user_id' => $user->id,
                    'source' => $this->source,
                    'source_id' => $this->source_id,
                ]);
                if (!$auth->save()) {
                    $auth->throwValidationException();
                }
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

    public function getUser()
    {
        return new User();
    }

}