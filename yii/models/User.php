<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string $password
 * @property string|null $name
 * @property string $email
 * @property int $role
 * @property string|null $description
 * @property string|null $auth_key
 * @property string|null $access_token
 * @property string|null $password_reset_token
 *
 * @property ProjectStaff[] $projectStaff
 * @property Project[] $projects
 */
class User extends ActiveRecord implements IdentityInterface
{
    public $newPassword;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'password', 'email', 'role'], 'required'],
            [['role'], 'integer'],
            [['description'], 'string'],
            [['username', 'password', 'newPassword', 'name', 'email'], 'string', 'max' => 255],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['access_token'], 'unique'],
            [['password_reset_token'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'password' => 'Password',
            'name' => 'Name',
            'email' => 'Email',
            'role' => 'Role',
            'description' => 'Description',
            'auth_key' => 'Auth Key',
            'access_token' => 'Access Token',
            'password_reset_token' => 'Password Reset Token',
        ];
    }

    public function getRole()
    {
        return $this->role; // Trường role trong bảng cơ sở dữ liệu lưu giá trị của vai trò
    }

    /**
     * Tạo mã đặt lại mật khẩu và lưu vào trường password_reset_token.
     *
     * @return string
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Tìm người dùng dựa trên mã đặt lại mật khẩu (reset token).
     *
     * @param string $token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne(['password_reset_token' => $token]);
    }

    /**
     * Kiểm tra xem mã đặt lại mật khẩu có hợp lệ hay không.
     *
     * @param string $token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expirationTime = Yii::$app->params['user.passwordResetTokenExpire']; // Thời gian hết hạn (cấu hình trong params)

        return $timestamp + $expirationTime >= time();
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert == true) {
                $this->password = Yii::$app->security->generatePasswordHash($this->password);
            } else {
                if (!empty($this->newPassword)) {
                    $this->password = Yii::$app->security->generatePasswordHash($this->newPassword);
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Gets query for [[ProjectStaff]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectStaff()
    {
        return $this->hasMany(ProjectStaff::class, ['userId' => 'id']);
    }

    /**
     * Gets query for [[Projects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjects()
    {
        return $this->hasMany(Project::class, ['projectManagerId' => 'id']);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return User|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * Finds an identity by the given ID.
     *
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface|null the identity object that matches the given ID.
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * Finds an identity by the given token.
     *
     * @param string $token the token to be looked for
     * @return IdentityInterface|null the identity object that matches the given token.
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    /**
     * @return int|string current user ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string|null current user auth key
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @param string $authKey
     * @return bool|null if auth key is valid for current user
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }
}
