<?php
namespace app\modules\medical\models\orm;

use app\common\db\ActiveRecord;
use app\common\helpers\CommonHelper;
use app\common\validators\ForeignKeyValidator;

/**
 * Class Policy
 *
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class Policy extends ActiveRecord
{
    // todo вынос в трейт

    /**
     * @return array
     */
    public static function types()
    {
        return [
            1 => 'ОМС',
            2 => 'ДМС',
        ];
    }

    /**
     * @return string|null
     */
    public function getTypeName()
    {
        $types = static::types();
        return isset($types[$this->type]) ? $types[$this->type] : null;
    }

    public function getInsurance()
    {
        return $this->hasOne(Insurance::class, ['id' => 'insurance_id']);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ [ 'number', 'type', 'insurance_id'], 'required' ],
            [
                [ 'expiration_date', 'issue_date' ],
                'filter',
                'filter' => function ($value) {
                    if (!empty($value)) {
                        return \Yii::$app->formatter->asDate($value, CommonHelper::FORMAT_DATE_DB);
                    }
                },
            ],
            [ ['insurance_id', 'patient_id'], ForeignKeyValidator::class ],
            [
                [ 'expiration_date',
                'issue_date',
                'number',
                'series',
                'type'
                ],
                'string'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabelsOverride()
    {
        return [
            'expiration_date' => 'Дата окончания',
            'issue_date' => 'Дата оформления',
            'insurance_id' => 'Страховая компания',
            'number' => 'Номер',
            'series' => 'Серия',
            'type' => 'Тип',
            'patient_id' => 'Пациент',
        ];
    }
}
