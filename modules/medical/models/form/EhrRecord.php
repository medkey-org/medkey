<?php
namespace app\modules\medical\models\form;

use app\common\base\Model;
use app\common\helpers\CommonHelper;
use app\common\validators\ForeignKeyValidator;
use app\modules\medical\MedicalModule;

class EhrRecord extends Model
{
    public $id;
    public $ehr_id;
    public $employee_id;
    public $template;
    public $conclusion;
    public $type;
    public $datetime;
    public $name;
    public $complaints;
    public $diagnosis;
    public $recommendations;
    public $preliminary;
    public $revisit;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [ ['ehr_id', 'employee_id', 'name'], 'required',],
            [ ['ehr_id', 'employee_id'], ForeignKeyValidator::class ],
            [ ['template', 'conclusion', 'name', 'complaints', 'diagnosis', 'recommendations'], 'string' ],
            [ 'preliminary', 'boolean'],
            [ ['type'], 'integer' ],
            [ ['revisit', 'datetime'],
                'datetime',
                'skipOnEmpty' => true,
                'format' => CommonHelper::FORMAT_DATETIME_UI,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'template' => MedicalModule::t('ehr', 'Template'),
            'conclusion' => MedicalModule::t('ehr', 'Conclusion'),
            'datetime' => MedicalModule::t('ehr', 'Visit date'),
            'type' => MedicalModule::t('ehr', 'Visit type'),
            'revisit' => MedicalModule::t('ehr', 'Repeated visit'),
            'preliminary' => MedicalModule::t('ehr', 'Preliminary diagnosis'),
            'recommendations' => MedicalModule::t('ehr', 'Recommendations'),
            'diagnosis' => MedicalModule::t('ehr', 'Diagnosis'),
            'complaints' => MedicalModule::t('ehr', 'Complaints'),
            'employee_id' => MedicalModule::t('ehr', 'Doctor'),
            'name' => MedicalModule::t('ehr', 'Name'),
        ];
    }
}
