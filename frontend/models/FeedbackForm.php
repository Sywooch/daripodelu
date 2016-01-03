<?php

namespace frontend\models;

use yii;
use yii\base\Model;
use frontend\components\validators\EmailPhoneValidator;

class FeedbackForm extends Model
{
    public $emailPhone;
    public $message;
    private $uniqueId;
    private $subject;

    public function rules()
    {
        return [
            [['emailPhone', 'message'], 'string'],
            ['emailPhone', 'required', 'message' => Yii::t('app', 'Enter your contacts, please')],
            ['emailPhone', EmailPhoneValidator::className()],
            ['message', 'required', 'message' => Yii::t('app', 'Enter your question, please')],
            [['emailPhone', 'message'], 'trim'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'emailPhone' => 'Ваш контактный e-mail или телефон',
            'message' => 'Ваш вопрос',
        ];
    }

    /**
     * Sends an email to the specified email address using the information collected by this model.
     *
     * @param  string  $email the target email address
     * @return boolean whether the email was sent
     */
    public function sendEmail($email)
    {
        $this->uniqueId = time();

        $this->subject = 'Вопрос с сайта "' . yii::$app->config->siteName . '"';

        return Yii::$app->mailer->compose(['html' => '@app/views/mail-templates/mail-to-admin'], ['mail' => $this])
            ->setTo($email)
            ->setSubject($this->subject)
            ->setTextBody($this->body)
            ->send();
    }
}