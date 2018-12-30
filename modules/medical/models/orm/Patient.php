<?php
namespace app\modules\medical\models\orm;

use app\common\db\ActiveRecord;
use app\common\logic\orm\EmailTrait;
use app\common\logic\orm\HumanTrait;
use app\common\logic\orm\PassportTrait;
use app\common\logic\orm\PhoneTrait;
use app\common\logic\orm\AddressTrait;
use app\common\helpers\CommonHelper;

/**
 * Class Patient
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
            'last_name' => 'Фамилия',
            'first_name' => 'Имя',
            'middle_name' => 'Отчество',
            'birthday' => 'Дата рождения',
            'snils' => 'СНИЛС',
            'inn' => 'ИНН',
            'birthplace' => 'Место рождения',
            'race_type' => 'Раса',
            'children_count' => 'Кол-во детей',
            'education_type' => 'Образование',
            'citizenship' => 'Гражданство',
            'sex' => 'Пол',
            'status' => 'Статус',
            'fullName' => 'Полное имя',
            'phones' => 'Телефоны',
            'emails' => 'Emails',
            'addresses' => 'Адреса',
            'passport' => 'Паспортные данные',
        ];
    }

    public function getPolicies()
    {
        return $this->hasMany(Policy::class, ['patient_id' => 'id']);
    }
}
