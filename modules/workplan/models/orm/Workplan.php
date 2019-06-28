<?php
namespace app\modules\workplan\models\orm;

use app\common\db\ActiveRecord;
use app\common\helpers\CommonHelper;
use app\common\validators\ForeignKeyValidator;
use app\modules\organization\models\orm\Cabinet;
use app\modules\organization\models\orm\Employee;
use app\modules\workplan\WorkplanModule;

/**
 * Class Workplan
 *
 * @property string $id
 * @property int $status
 * @property string $employee_id
 * @property-read int[] $weekIds
 * @property-read WorkplanToWeek $workplanToWeeks
 * @property-read Employee $employee
 *
 * @package Module\Workplan
 * @copyright 2012-2019 Medkey
 */
class Workplan extends ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;

    public static function modelIdentity()
    {
        return ['since_date', 'expire_date', 'since_time', 'expire_time', 'employee_id', 'cabinet_id', 'department_id', 'status'];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['status', 'integer'],
            [ ['employee_id', 'department_id', 'cabinet_id'],
                ForeignKeyValidator::class ],
            [ ['since_date', 'expire_date', 'since_time', 'expire_time', 'employee_id', 'cabinet_id' ],
                'required',
                'on' => ['create', 'update']
            ],
            [ ['since_date', 'expire_date'],
                'filter',
                'filter' => function ($value) {
                    return \Yii::$app->formatter->asDate($value, CommonHelper::FORMAT_DATE_DB);
                },
                'on' => ['create', 'update']
            ],
            [ ['since_time', 'expire_time'],
                'time',
                'format' => CommonHelper::FORMAT_TIME_DB,
                'on' => ['create', 'update']
            ],
        ];
    }

    /**
     * @todo по сути этот метод тут и не нужен. Вынести в форму
     * @return int[]
     * @deprecated todo перенести в форму
     */
    public function getWeekIds()
    {
        $w = $this->workplanToWeeks;
        $weekIds = [];
        foreach ($w as $week) {
            array_push($weekIds, $week->week);
        }
        return $weekIds;
    }

    public function getEmployee()
    {
        return $this->hasOne(Employee::class, ['id' => 'employee_id']);
    }

    public function getCabinet()
    {
        return $this->hasOne(Cabinet::class, ['id' => 'cabinet_id']);
    }

    public function getWorkplanToWeeks()
    {
        return $this->hasMany(WorkplanToWeek::class, ['workplan_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabelsOverride()
    {
        return [
            'since_date' => WorkplanModule::t('workplan', 'Start date'),
            'expire_date' => WorkplanModule::t('workplan', 'End date'),
            'since_time' => WorkplanModule::t('workplan', 'Start time'),
            'expire_time' => WorkplanModule::t('workplan', 'End time'),
            'department_id' => WorkplanModule::t('workplan', 'Department'),
            'cabinet_id' => WorkplanModule::t('workplan', 'Cabinet'),
            'weekIds' => WorkplanModule::t('workplan', 'Week days'),
            'employee_id' => WorkplanModule::t('workplan', 'Employee'),
        ];
    }
}
