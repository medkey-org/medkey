<?php
namespace app\modules\security\models\orm;

use app\common\db\ActiveRecord;
use app\common\validators\ForeignKeyValidator;
use app\modules\organization\models\orm\Employee;
use app\modules\security\SecurityModule;
use yii\db\ActiveQueryInterface;
use yii\web\IdentityInterface;

/**
 * Class User
 *
 * @property string $auth_key
 * @property string $login
 * @property string $password_hash
 * @property int $status
 * @property string $language
 * @property-read int $statusName
 * @property-read AclRole $aclRole
 *
 * @package Module\Security
 * @copyright 2012-2019 Medkey
 */
class User extends ActiveRecord implements IdentityInterface
{
    const SCENARIO_CHANGE_PASSWORD = 'change-password';
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;

    /**
     * @return array
     */
    public static function modelIdentity()
    {
        return ['login'];
    }

    /**
     * @return array
     */
    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE => SecurityModule::t('user','Active'),
            self::STATUS_INACTIVE => SecurityModule::t('user','Inactive'),
        ];
    }

    /**
     * @return int
     */
    public function getStatusName()
    {
        $statuses = self::statuses();
        return !isset($statuses[$this->status]) ? '' : $statuses[$this->status];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                'login',
                'unique',
                'filter' => function (ActiveQueryInterface $query) {
                    return $query->notDeleted();
                },
                'on' => [ActiveRecord::SCENARIO_CREATE, ActiveRecord::SCENARIO_UPDATE]
            ],
            [ ['acl_role_id'], ForeignKeyValidator::class, ],
            [ ['status'], 'integer', ],
            [ ['login', 'language'], 'string' ],
            [ ['login', 'password_hash'], 'required', 'on' => ActiveRecord::SCENARIO_CREATE ],
            [ ['password_hash'], 'filter', 'filter' => function ($value) {
                return \Yii::$app->security->generatePasswordHash($value);
            }, 'on' => ['create', self::SCENARIO_CHANGE_PASSWORD] ],
        ];
    }

    public function getEmployees()
    {
        return $this->hasMany(Employee::class, ['user_id' => 'id']);
    }

    public function getEmployee()
    {
        return $this->hasOne(Employee::class, ['user_id' => 'id']);
    }

    public function getAclRole()
    {
        return $this->hasOne(AclRole::class, ['id' => 'acl_role_id']);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        $record = static::find()
            ->where([
                '[[user]].[[id]]' => $id,
                '[[user]].[[status]]' => self::STATUS_ACTIVE,
            ]);
        if (getenv('AUTH_WITH_EMPLOYEE')) {
            $record->innerJoinWith('employee');
        }
        return $record->notDeleted()->one();
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $record = static::find()
            ->where([
                '[[user]].[[access_token]]' => $token,
                '[[user]].[[status]]' => self::STATUS_ACTIVE,
            ]);
        if (getenv('AUTH_WITH_EMPLOYEE')) {
             $record->innerJoinWith('employee');
        }
        return $record->notDeleted()->one();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
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
     * @inheritdoc
     */
    public function attributeLabelsOverride()
    {
        return [
            'login' => SecurityModule::t('user','User login'),
            'password_hash' => SecurityModule::t('user','Password'),
            'acl_role_id' => SecurityModule::t('user','Role'),
            'status' => SecurityModule::t('user','Status'),
            'statusName' => SecurityModule::t('user','Status'),
        ];
    }
}
