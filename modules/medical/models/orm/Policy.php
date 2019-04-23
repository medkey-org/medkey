<?php
namespace app\modules\medical\models\orm;

use app\common\db\ActiveRecord;
use app\common\helpers\CommonHelper;
use app\common\validators\ForeignKeyValidator;
use app\modules\medical\MedicalModule;

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
            1 => MedicalModule::t('policy', 'Obligatory health insurance'),
            2 => MedicalModule::t('policy', 'Voluntary health insurance'),
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
            'expiration_date' => MedicalModule::t('policy', 'Expiration date'),
            'issue_date' => MedicalModule::t('policy', 'Issue date'),
            'insurance_id' => MedicalModule::t('policy', 'Insurance organization'),
            'number' => MedicalModule::t('policy', 'Number'),
            'series' => MedicalModule::t('policy', 'Series'),
            'type' => MedicalModule::t('policy', 'Type'),
            'patient_id' => MedicalModule::t('policy', 'Patient'),
        ];
    }
}
