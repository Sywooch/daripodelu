<?php

namespace frontend\components\validators;


use yii;
use yii\base\InvalidConfigException;
use yii\helpers\Json;
use yii\validators\Validator;
use yii\validators\EmailValidator;
use yii\validators\PunycodeAsset;
use yii\validators\RegularExpressionValidator;
use yii\validators\ValidationAsset;
use yii\web\JsExpression;

class EmailPhoneValidator extends Validator
{
    /**
     * @var string the regular expression used to validate the attribute value.
     * @see http://www.regular-expressions.info/email.html
     */
    public $emailPattern = '[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?';
    /**
     * @var string the regular expression used to validate email addresses with the name part.
     * This property is used only when [[allowName]] is true.
     * @see allowName
     */
    public $fullEmailPattern = '[^@]*<[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?>';
    /**
     * @var string the regular expression used to validate phone number
     */
    public $phonePattern = '((8|\+7|\+[0-9]{1,3})[\- ]?)?(\(?\d{2,5}\)?[\- ]?)?[\d\- ]{6,10}';
    /**
     * @var boolean whether to allow name in the email address (e.g. "John Smith <john.smith@example.com>"). Defaults to false.
     * @see fullEmailPattern
     */
    public $allowName = false;
    /**
     * @var boolean whether to check whether the email's domain exists and has either an A or MX record.
     * Be aware that this check can fail due to temporary DNS problems even if the email address is
     * valid and an email would be deliverable. Defaults to false.
     */
    public $checkDNS = false;
    /**
     * @var boolean whether validation process should take into account IDN (internationalized domain
     * names). Defaults to false meaning that validation of emails containing IDN will always fail.
     * Note that in order to use IDN validation you have to install and enable `intl` PHP extension,
     * otherwise an exception would be thrown.
     */
    public $enableIDN = false;

    public function init()
    {
        parent::init();
        if ($this->enableIDN && !function_exists('idn_to_ascii')) {
            throw new InvalidConfigException('In order to use IDN validation intl extension must be installed and enabled.');
        }

        if ($this->message === null)
        {
            $this->message = Yii::t('app', 'Enter correct email or phone number, please');
        }
    }

    public function validateAttribute($model, $attribute)
    {
        $emailValidator = new EmailValidator();
        $matchValidator = new RegularExpressionValidator([
            'pattern' => '/^' . $this->phonePattern .  '$/',
        ]);
        if ( ! $emailValidator->validate($model->$attribute) && ! $matchValidator->validate($model->$attribute))
        {
            $this->addError($attribute, $this->message);
        }
    }

    public function clientValidateAttribute($model, $attribute, $view)
    {
        $options = [
            'pattern' => new JsExpression('/^((' . $this->emailPattern . ')|(' . $this->phonePattern . '))$/'),
            'fullPattern' => new JsExpression('/^((' . $this->fullEmailPattern . ')|(' . $this->phonePattern . '))$/'),
            'allowName' => $this->allowName,
            'message' => Yii::$app->getI18n()->format($this->message, [
                'attribute' => $model->getAttributeLabel($attribute),
            ], Yii::$app->language),
            'enableIDN' => (bool) $this->enableIDN,
        ];
        if ($this->skipOnEmpty) {
            $options['skipOnEmpty'] = 1;
        }

        ValidationAsset::register($view);
        if ($this->enableIDN) {
            PunycodeAsset::register($view);
        }

        return 'yii.validation.regularExpression(value, messages, ' . Json::htmlEncode($options) . ');';
    }
}