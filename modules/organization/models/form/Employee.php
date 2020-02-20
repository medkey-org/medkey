<?php
namespace app\modules\organization\models\form;

use app\common\base\Model;
use app\common\helpers\CommonHelper;
use app\common\logic\orm\HumanTrait;
use app\common\validators\ForeignKeyValidator;
use app\modules\organization\OrganizationModule;
use yii\validators\EmailValidator;
use yii\validators\RequiredValidator;

/**
 * Class Employee
 * @package Module\Organization
 * @copyright 2012-2019 Medkey
 */
class Employee extends Model
{
    use HumanTrait;

    const SEX_MALE = 1;
    const SEX_FEMALE = 2;

    public $id;
    public $user_id;
    public $first_name;
    public $middle_name;
    public $last_name;
    public $status;
    public $sex;
    public $education;
    public $birthday;
    public $department_id;
    public $speciality_id;
    public $position_id;
    public $addresses;
    public $emails;

    public $phones = [];

    // relations from AR
    public $specialities;
    public $user;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['specialities', 'safe'],
            [ ['phones'],
                'validatePhones',
            ],
            [ ['emails'],
                'validateEmails',
            ],
            [ ['addresses'],
                'validateAddresses',
            ],
            [ ['first_name', 'last_name', 'sex', 'birthday', 'user_id'],
                'required',
            ],
            [ ['user_id', 'position_id'], ForeignKeyValidator::class, ],
            [ ['first_name', 'last_name', 'middle_name'],
                'string',
            ],
            [ ['first_name', 'middle_name', 'last_name'], 'match', 'pattern' => '/^[АБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЬЫЪЭЮЯабвгдеёжзийклмнопрстуфхцчшщьыъэюяa-zA-Z\-]+$/' ],
            [ ['status', 'sex', 'education'],
                'integer',
            ],
//            [ 'speciality_id', ForeignKeyValidator::class ],
            [ ['birthday'],
                'filter',
                'filter' => function () {
                    return $this->birthday = \Yii::$app->formatter->asDate($this->birthday, CommonHelper::FORMAT_DATE_DB);
                },
                'skipOnEmpty' => true,
            ],
        ];
    }

    public function validateAddresses($attribute)
    {

    }

    /**
     * @param array $attribute
     * @return null|void
     */
    public function validateEmails($attribute)
    {
        $emailValidator = new EmailValidator();

        if (!is_array($this->{$attribute})) {
            return null;
        }
        foreach ($this->{$attribute} as $index => $row) {
            $error = null;
            if (empty($row['address'])) {
                continue;
            }
            $emailValidator->validate($row['address'], $error);
            if (!empty($error)) {
                $key = $attribute . '[' . $index . '][address]';
                $this->addError($key, $error);
            }
        }

        $requireValidator = new RequiredValidator();

        if (!is_array($this->{$attribute})) {
            return null;
        }
        foreach ($this->{$attribute} as $index => $row) {
            if ($index === 0) { // not required first row
                continue;
            }
            $error = null;
            $requireValidator->validate($row['type'], $error);
            if (!empty($error)) {
                $key = $attribute . '[' . $index . '][type]';
                $this->addError($key, $error);
            }
            $requireValidator->validate($row['address'], $error);
            if (!empty($error)) {
                $key = $attribute . '[' . $index . '][address]';
                $this->addError($key, $error);
            }
        }
    }

    /**
     * @param array $attribute
     * @return null|void
     */
    public function validatePhones($attribute)
    {
//        $requiredValidator = new RequiredValidator();

        if (!is_array($this->{$attribute})) {
            return null;
        }
        foreach ($this->{$attribute} as $index => $row) {
            if (empty($row['type']) && empty($row['phone'])) { // не required если не заполнено вообще
                continue;
            }
            if (!empty($row['type']) && empty($row['phone'])) {
                $key = $attribute . '[' . $index . '][phone]';
                $this->addError($key, 'Необходимо заполнить значение.');
            } elseif (empty($row['type']) && !empty($row['phone'])) {
                $key = $attribute . '[' . $index . '][type]';
                $this->addError($key, 'Необходимо заполнить значение.');
            }
//            $error = null;
//            $requiredValidator->validate($row['phone'], $error);
//            if (!empty($error)) {
//                $key = $attribute . '[' . $index . '][phone]';
//                $this->addError($key, $error);
//            }
//            $requiredValidator->validate($row['type'], $error);
//            if (!empty($error)) {
//                $key = $attribute . '[' . $index . '][type]';
//                $this->addError($key, $error);
//            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => OrganizationModule::t('common', 'User'),
            'last_name' => OrganizationModule::t('common', 'Last name'),
            'first_name' => OrganizationModule::t('common', 'First name'),
            'middle_name' => OrganizationModule::t('common', 'Middle name'),
            'birthday' => OrganizationModule::t('common', 'Birthday'),
            'sex' => OrganizationModule::t('common', 'Sex'),
            'emails' => OrganizationModule::t('common', 'E-mails'),
            'addresses' => OrganizationModule::t('common', 'Addresses'),
            'phones' => OrganizationModule::t('employee', 'Phones'),
            'specialities' => OrganizationModule::t('employee', 'Specialities'),
            'position_id' => OrganizationModule::t('employee', 'Position')
        ];
    }
}
