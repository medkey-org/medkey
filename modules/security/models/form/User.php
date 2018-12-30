<?php
namespace app\modules\security\models\form;

use app\common\base\Model;
use app\common\validators\ForeignKeyValidator;
use app\modules\security\models\orm\User as UserOrm;

/**
 * Class User
 * @package Module\Security
 * @copyright 2012-2019 Medkey
 */
class User extends Model
{
    public $id;
    public $login;
    public $password_hash;
    public $status;
    public $acl_role_id;
    public $auth_key;

    public $aclRole = [];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                'login',
                'validateUniqueLogin',
            ],
            [ 'status', 'integer' ],
            [ ['acl_role_id'], ForeignKeyValidator::class, ],
            [ ['login', 'password_hash'], 'string' ],
            [ ['login',], 'required', 'on' => ['create', 'update'], ],
            [ ['password_hash'], 'required', 'on' => ['create'] ],
            [ ['password_hash'], 'filter', 'filter' => function ($value) {
                return \Yii::$app->security->generatePasswordHash($value);
            }, 'on' => 'create' ],
        ];
    }

    /**
     * @return int
     */
    public function getStatusName()
    {
        $statuses = UserOrm::statuses();
        return !isset($statuses[$this->status]) ? '' : $statuses[$this->status];
    }

    /**
     * @param $attribute
     * @return void
     */
    public function validateUniqueLogin($attribute)
    {
        $user = UserOrm::find()
            ->where([
                'login' => $this->login,
            ])
            ->andFilterWhere([
                '<>',
                'id',
                $this->id
            ])
            ->notDeleted()
            ->one();
        if (isset($user)) {
            $this->addError($attribute, 'Указанный пользователь уже существует в БД.');
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'login' => 'Логин',
            'password_hash' => 'Пароль',
            'acl_role_id' => 'Роль',
            'aclRole' => 'Роль',
            'status' => 'Статус',
        ];
    }
}
