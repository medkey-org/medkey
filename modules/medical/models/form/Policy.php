<?php
namespace app\modules\medical\models\form;

use app\common\base\Model;
use app\common\helpers\CommonHelper;
use app\common\validators\ForeignKeyValidator;
use app\modules\medical\MedicalModule;

/**
 * Class Policy
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class Policy extends Model
{
    public $id;
    public $expiration_date;
    public $issue_date;
    public $insurance_id;
    public $number;
    public $series;
    public $type;
    public $patient_id;


    /**
     * @inheritdoc
     */
    public function init()
    {
        if (!empty($this->expiration_date)) {
            $this->expiration_date = \Yii::$app->formatter->asDate($this->expiration_date, CommonHelper::FORMAT_DATE_UI);
        }
        if (!empty($this->issue_date)) {
            $this->issue_date = \Yii::$app->formatter->asDate($this->issue_date, CommonHelper::FORMAT_DATE_UI);
        }
    }

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

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
                [ [ 'issue_date', 'expiration_date' ], 'date', 'format' => CommonHelper::FORMAT_DATE_UI ],
                [ [ 'number', 'type', 'insurance_id' ], 'required' ],
                [ ['insurance_id', 'patient_id'], ForeignKeyValidator::class, ],
                [ [
                    'expiration_date',
                    'issue_date',
                    'number',
                    'series',
                    'type',
                ],
                'string' ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
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
