<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

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
 *
 * @property ProjectStaff[] $projectStaff
 * @property Project[] $projects
 */
class User extends \yii\db\ActiveRecord
{
    public $newPassword;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
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
            [['username', 'password', 'name', 'email'], 'string', 'max' => 255],
            [['username'], 'unique'],
            [['email'], 'unique'],
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
        ];
    }
    

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (!empty($this->newPassword)) {
                $this->password = Yii::$app->security->generatePasswordHash($this->newPassword);
                $this->newPassword = null;
            } else {
                $this->password = Yii::$app->security->generatePasswordHash($this->password);
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
}
