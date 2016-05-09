<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $last_name
 * @property string $first_name
 * @property string $middle_name
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $role
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property string $password write-only password
 *
 * @property string $roleName
 * @property string $statusName
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_BLOCKED = 0;
    const STATUS_ACTIVE = 10;

    const ROLE_GUEST = 1;
    const ROLE_MODERATOR = 5;
    const ROLE_ADMIN = 10;

    const SCENARIO_REGISTER = 'register';
    const SCENARIO_CHANGE_PASSWORD = 'change_password';

    public $password;
    public $password_repeat;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'email', 'role', 'status'], 'required'],

            [['last_name', 'first_name', 'middle_name', ], 'string', 'max' => 30],

            ['username', 'unique', 'targetClass' => self::className(), 'message' => Yii::t('app', 'This username has already been taken.')],
            ['username', 'string', 'min' => 2, 'max' => 255],
            ['username', 'match', 'pattern' => '/^\w+[\w_\-\d]+$/i'],

            [['password', 'password_repeat'], 'required', 'on' => [static::SCENARIO_REGISTER, static::SCENARIO_CHANGE_PASSWORD]],
            ['password', 'string', 'min' => 6, 'max' => 40],
            ['password_repeat', 'compare', 'compareAttribute' => 'password', 'message' => Yii::t('app', "Passwords don't match."), 'on' => [static::SCENARIO_REGISTER, static::SCENARIO_CHANGE_PASSWORD] ],

            ['email', 'email'],
            ['email', 'unique', 'targetClass' => self::className(), 'message' => Yii::t('app', 'This email address has already been taken.')],
            ['email', 'string', 'max' => 255],

            [['role', 'status'], 'integer'],
            ['role', 'default', 'value' => self::ROLE_MODERATOR],
            ['role', 'in', 'range' => [self::ROLE_GUEST, self::ROLE_MODERATOR, self::ROLE_ADMIN]],

            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_BLOCKED]],

            [['username', 'email', 'last_name', 'first_name', 'middle_name', ], 'trim'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'username' => Yii::t('app', 'Логин'),
            'email' => Yii::t('app', 'Email'),
            'password' => Yii::t('app', 'Пароль'),
            'password_repeat' => Yii::t('app', 'Пароль еще раз'),
            'last_name' => Yii::t('app', 'Фамилия'),
            'first_name' => Yii::t('app', 'Имя'),
            'middle_name' => Yii::t('app', 'Отчество'),
            'status' => Yii::t('app', 'Статус'),
            'role' => Yii::t('app', 'Роль'),
            'created_at' => Yii::t('app', 'Дата создания'),
            'updated_at' => Yii::t('app', 'Дата последнего обновления'),
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
     * @return static|null
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
        if ( !static::isPasswordResetTokenValid($token))
        {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
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
        if (empty($token))
        {
            return false;
        }

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];

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
    public function setPasswordHash($password)
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

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert))
        {
            if ($this->isNewRecord)
            {
                $this->auth_key = Yii::$app->security->generateRandomString();
            }

            return true;
        }

        return false;
    }

    public static function getRoles()
    {
        return [
            self::ROLE_MODERATOR => 'Модератор',
            self::ROLE_ADMIN => 'Администратор',
        ];
    }

    public static function getRoleNameById($index)
    {
        $roles = self::getRoles();

        return $roles[$index];
    }

    public function getRoleName()
    {
        $roles = static::getRoles();

        return $roles[$this->role];
    }

    /**
     * Returns the role string Id
     *
     * For example, returns guest, admin and etc.
     *
     * @param integer $index
     * @return string
     */
    public static function getRoleStringId($index)
    {
        $roleStringIds = [
            self::ROLE_GUEST => 'guest',
            self::ROLE_MODERATOR => 'moderator',
            self::ROLE_ADMIN => 'admin',
        ];

        return $roleStringIds[$index];
    }

    public static function getStatuses()
    {
        return [
            static::STATUS_BLOCKED => 'Заблокирован',
            static::STATUS_ACTIVE => 'Активен',
        ];
    }

    public function getStatusName()
    {
        $statuses = static::getStatuses();

        return $statuses[$this->status];
    }

    /**
     * Returns status name by status Id
     *
     * @param integer $statusId
     * @return mixed
     */
    public static function getStatusNameById($statusId)
    {
        $statuses = static::getStatuses();

        return $statuses[$statusId];
    }
}
