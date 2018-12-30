<?php
namespace app\modules\medical\models\orm;

use app\common\db\ActiveRecord;
use app\common\validators\ForeignKeyValidator;
use app\modules\organization\models\orm\Employee;

/**
 * Class EhrRecord
 *
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class EhrRecord extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ ['ehr_id', 'employee_id', 'conclusion'], 'required',],
            [ ['ehr_id', 'employee_id'], ForeignKeyValidator::class ],
            [ ['template', 'conclusion'], 'string' ],
            [ ['type'], 'integer' ]
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
            'ehr_id' => 'Мед. карта',
            'employee_id' => 'Сотрудник',
            'template' => 'Шаблон',
            'conclusion' => 'Заключение',
            'datetime' => 'Время',
            'type' => 'Тип',
        ];
    }
}
