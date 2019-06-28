<?php
namespace app\modules\medical\models\orm;

use app\common\db\ActiveRecord;
use app\common\logic\orm\EmailTrait;
use app\common\logic\orm\HumanTrait;
use app\common\logic\orm\PassportTrait;
use app\common\logic\orm\PhoneTrait;
use app\common\logic\orm\AddressTrait;
use app\common\helpers\CommonHelper;
use app\modules\medical\MedicalModule;

/**
 * Patient ORM
 *
 * @property string $last_name
 * @property string $first_name
 * @property string $middle_name
 * @property int $birthday
 * @property string $snils
 * @property string $inn
 * @property string $birthplace
 * @property int $race_type
 * @property int $children_count
 * @property int $education_type
 * @property int $citizenship
 * @property int $sex
 * @property int $status
 * @property string $passport
 * @property int|string $patient_id
 * @property-read string $fullName
 * @property-read string[] $phones
 * @property-read string[] $emails
 * @property-read string[] $addresses
 *
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class Patient extends ActiveRecord
{
    const SEX_MALE = 1;
    const SEX_FEMALE = 2;

    use HumanTrait;
    use PhoneTrait;
    use EmailTrait;
    use AddressTrait;
    use PassportTrait;

    public static function modelIdentity()
    {
        return ['first_name', 'last_name', 'sex', 'birthday'];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ ['first_name', 'last_name', 'sex', 'birthday'],
                'required',
                'on' => ['create', 'update']
            ],
            [ ['first_name', 'last_name', 'middle_name', 'snils', 'inn'],
                'string',
                'on' => ['create', 'update']
            ],
            [ ['status', 'sex'],
                'integer',
                'on' => ['create', 'update']
            ],
            [ ['birthday'],
                'filter',
                'filter' => function ($value) {
                    return \Yii::$app->formatter->asDate(empty($value) ? null : $value, CommonHelper::FORMAT_DATE_DB);
                },
                'on' => ['create', 'update']
            ],
        ];
    }

    public function attributeLabelsOverride()
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEhr()
    {
        return $this->hasOne(Ehr::class, ['patient_id' => 'id'])
            ->where([
                '[[ehr]].[[status]]' => Ehr::STATUS_ACTIVE
            ]);
    }

    public function getPolicies()
    {
        return $this->hasMany(Policy::class, ['patient_id' => 'id']);
    }
}
