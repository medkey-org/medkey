<?php
namespace app\modules\medical\models\orm;

use app\common\db\ActiveRecord;
use app\common\helpers\CommonHelper;
use app\common\validators\ForeignKeyValidator;
use app\modules\medical\MedicalModule;
use app\modules\organization\models\orm\Employee;

/**
 * EHR Record ORM
 *
 * @property int|string $ehr_id
 * @property int|string $employee_id
 * @property string $conclusion
 * @property string $template
 * @property int $datetime
 * @property int $type
 *
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class EhrRecord extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [ ['ehr_id', 'employee_id'], 'required',],
            [ ['ehr_id', 'employee_id'], ForeignKeyValidator::class ],
            [ ['template', 'conclusion', 'name', 'complaints', 'diagnosis', 'recommendations', 'preliminary'], 'string' ],
            [ ['type'], 'integer' ],
            [ ['revist', 'datetime'],
                'datetime',
                'format' => CommonHelper::FORMAT_DATETIME_DB,
            ],
        ];
    }

    public function getEmployee()
    {
        return $this->hasOne(Employee::class, ['id' => 'employee_id']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabelsOverride()
    {
        return [
            'ehr_id' => MedicalModule::t('ehr', 'EHR'),
            'employee_id' => MedicalModule::t('ehr', 'Doctor'),
            'template' => MedicalModule::t('ehr', 'Template'),
            'conclusion' => MedicalModule::t('ehr', 'Conclusion'),
            'datetime' => MedicalModule::t('ehr', 'Visit date'),
            'type' => MedicalModule::t('ehr', 'Visit type'),
        ];
    }
}
