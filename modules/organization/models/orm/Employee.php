<?php
namespace app\modules\organization\models\orm;

use app\common\logic\orm\AddressTrait;
use app\common\logic\orm\EmailTrait;
use app\common\db\ActiveRecord;
use app\common\helpers\CommonHelper;
use app\common\logic\orm\HumanTrait;
use app\common\logic\orm\PhoneTrait;
use app\common\logic\orm\Phone;
use app\common\validators\ForeignKeyValidator;
use app\modules\organization\OrganizationModule;
use app\modules\security\models\orm\User;

/**
 * Class Employee
 *
 * @property string $user_id
 * @property string $first_name
 * @property string $middle_name
 * @property string $last_name
 * @property int $status
 * @property int $sex
 * @property int $education
 * @property string $birthday
 * @property-read int $sexName
 * @property-read string $fullName
 * @property-read Phone[] $phones
 *
 * @package Module\Organization
 * @copyright 2012-2019 Medkey
 */
class Employee extends ActiveRecord
{
    const SEX_MALE = 1;
    const SEX_FEMALE = 2;

    use HumanTrait;
    use PhoneTrait;
    use EmailTrait;
    use AddressTrait;

    /**
     * @inheritdoc
     */
    public static function modelIdentity()
    {
        return ['first_name', 'middle_name', 'last_name', 'birthday'];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ ['first_name', 'last_name', 'sex', 'birthday'],
                'required',
            ],
            [ ['first_name', 'middle_name', 'last_name'], 'match', 'pattern' => '/^[АБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЬЫЪЭЮЯабвгдеёжзийклмнопрстуфхцчшщьыъэюяa-zA-Z\-]+$/' ],

            [ ['first_name', 'last_name', 'middle_name' ],
                'string',
            ],
            [ ['user_id'], ForeignKeyValidator::class, ],
            [ ['status', 'sex', 'education'],
                'integer',
            ],
            [ ['birthday'],
                'filter',
                'filter' => function () {
                    return $this->birthday = \Yii::$app->formatter->asDate($this->birthday, CommonHelper::FORMAT_DATE_DB);
                },
                'skipOnEmpty' => true,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabelsOverride()
    {
        return [
            'user_id' => OrganizationModule::t('common', 'User'),
            'last_name' => OrganizationModule::t('common', 'Last name'),
            'first_name' => OrganizationModule::t('common', 'First name'),
            'middle_name' => OrganizationModule::t('common', 'Middle name'),
            'birthday' => OrganizationModule::t('common', 'Birthday'),
            'sex' => OrganizationModule::t('common', 'Sex'),
            'phones' => OrganizationModule::t('common', 'Phones'),
            'phone.phone' => OrganizationModule::t('common', 'Phone(s)'),
            'emails' => OrganizationModule::t('common', 'E-mail'),
        ];
    }
}
