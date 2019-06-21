<?php
namespace app\modules\medical\models\form;

use app\common\base\Model;
use app\common\helpers\CommonHelper;
use app\common\logic\orm\HumanTrait;
use app\modules\medical\MedicalModule;
use yii\validators\EmailValidator;
use yii\validators\RequiredValidator;

/**
 * Class Patient
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class Patient extends Model
{
    use HumanTrait;

    const SEX_MALE = 1;
    const SEX_FEMALE = 2;

    public $id;
    public $first_name;
    public $middle_name;
    public $last_name;
    public $birthday;
    public $snils;
    public $inn;
    public $birthplace;
    public $race_type;
    public $children_count;
    public $education_type;
    public $citizenship;
    public $sex;
    public $status;

    public $ehr;

    public $phones;
    public $emails;
    public $addresses;
    public $passportSeries;
    public $passportNumber;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ ['phones'],
                'validatePhones',
            ],
            [ ['emails'],
                'validateEmails',
            ],
            [ ['addresses'],
                'validateAddresses',
            ],
            [ ['passportSeries', 'passportNumber'], 'string' ], // todo each validator
            [ ['first_name', 'last_name', 'sex', 'birthday'],
                'required',
            ],
            [ ['first_name', 'last_name', 'middle_name', 'snils', 'inn'],
                'string',
            ],
//            [ ['inn'], InnValidator::class],
            [ ['status', 'sex'],
                'integer',
            ],
            [ ['birthday'],
                'filter',
                'filter' => function ($value) {
                    return \Yii::$app->formatter->asDate(empty($value) ? null : $value, CommonHelper::FORMAT_DATE_DB);
                },
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
//            if ($index === 0) { // not required first row
//                continue;
//            }
//            $error = null;
//            $requireValidator->validate($row['type'], $error);
//            if (!empty($error)) {
//                $key = $attribute . '[' . $index . '][type]';
//                $this->addError($key, $error);
//            }
//            $requireValidator->validate($row['address'], $error);
//            if (!empty($error)) {
//                $key = $attribute . '[' . $index . '][address]';
//                $this->addError($key, $error);
//            }
        }
    }

    /**
     * @param array $attribute
     * @return null|void
     */
    public function validatePhones($attribute)
    {
        $requiredValidator = new RequiredValidator();

        if (!is_array($this->{$attribute})) {
            return null;
        }
        foreach ($this->{$attribute} as $index => $row) {
            if (empty($row['type']) && empty($row['phone'])) { // не required если не заполнено вообще
                continue;
            }
            if (!empty($row['type']) && empty($row['phone'])) {
                $key = $attribute . '[' . $index . '][phone]';
                $this->addError($key, MedicalModule::t('common', 'Value can\'t be empty'));
            } elseif (empty($row['type']) && !empty($row['phone'])) {
                $key = $attribute . '[' . $index . '][type]';
                $this->addError($key, MedicalModule::t('common', 'Value can\'t be empty'));
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'last_name' => MedicalModule::t('patient','Last name'),
            'first_name' => MedicalModule::t('patient','First name'),
            'middle_name' => MedicalModule::t('patient','Middle name'),
            'birthday' => MedicalModule::t('patient','Date of birth'),
            'snils' => MedicalModule::t('patient','SSN'),
            'inn' => MedicalModule::t('patient','ITIN'),
            'birthplace' => MedicalModule::t('patient','Place of birth'),
            'race_type' => MedicalModule::t('patient','Race'),
            'children_count' => MedicalModule::t('patient','Children count'),
            'education_type' => MedicalModule::t('patient','Education'),
            'citizenship' => MedicalModule::t('patient','Citizenship'),
            'sex' => MedicalModule::t('patient','Sex'),
            'status' => MedicalModule::t('patient','Status'),
            'fullName' => MedicalModule::t('patient','Full name'),
            'phones' => MedicalModule::t('patient','Phone numbers'),
            'emails' => MedicalModule::t('patient','Emails'),
            'addresses' => MedicalModule::t('patient','Address list'),
            'passport' => MedicalModule::t('patient','Passport data'),

            'passportSeries' => MedicalModule::t('patient','Passport series'),
            'passportNumber' => MedicalModule::t('patient','Passport number'),
        ];
    }
}
