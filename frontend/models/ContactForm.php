<?php

namespace frontend\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class ContactForm extends Model
{
    public $first_name;
    public $last_name;
    public $email;
    public $subject;
    public $message;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['email', 'message', 'subject', 'first_name', 'last_name'], 'required'],
            // email has to be a valid email address
            ['email', 'email'],
            // verifyCode needs to be entered correctly
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'message' => 'Mensagem',
            'subject' => 'Assunto',
            'first_name' => 'Nome',
            'last_name' => 'Sobrenome',
        ];
    }

    /**
     * Sends an email to the specified email address using the information collected by this model.
     * @return boolean whether the email was sent
     */
    public function sendEmail()
    {
        return Yii::$app->mailer
            ->compose(
                [
                    'html' => 'feedback'
                ],
                [
                    'name' => $this->first_name . ' ' . $this->last_name,
                    'subject' => $this->subject,
                    'body' => $this->message,
                ]
            )
            ->setFrom([\Yii::$app->get('variables')->get('email', 'noreply') => \Yii::$app->name])
            ->setTo(\Yii::$app->get('variables')->get('email', 'contact'))
            ->setReplyTo($this->email)
            ->setSubject('You have new feedback')
            ->send();
    }
}
