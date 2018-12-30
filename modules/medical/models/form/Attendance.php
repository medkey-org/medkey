<?php
namespace app\modules\medical\models\form;

use app\common\base\Model;
use app\common\helpers\CommonHelper;
use app\common\validators\ForeignKeyValidator;
use app\modules\medical\models\orm\Attendance as AttendanceORM;

/**
 * Class Attendance
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class Attendance extends Model
{
    public $id;
    public $ehr_id;
    public $workplan_id;
    public $status;
    public $datetime;
    public $type;
    public $employee_id;
    public $number;

    public $ehr;
    public $employee;
    public $workplan;
    public $referrals;

    /**
     * @return string
     */
    public function typeName()
    {
        $types = AttendanceORM::types();
        return !isset($types[$this->type]) ? '' : $types[$this->type];
    }

    /**
     * @return string
     */
    public function getStatusName()
    {
        $statuses = AttendanceORM::statuses();
        return !isset($statuses[$this->status]) ? '' : $statuses[$this->status];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ 'datetime', 'required' ],
            [ 'datetime', 'filter', 'filter' => function ($value) {
                return \Yii::$app->formatter->asDatetime($value . date_default_timezone_get(), CommonHelper::FORMAT_DATETIME_DB);
            } ],
            [ 'type', 'default', 'value' => AttendanceORM::TYPE_TIME, ],
            [ 'status', 'default', 'value' => AttendanceORM::STATUS_NEW, ],
            [ ['type', 'status'], 'integer' ],
            [ ['ehr_id', 'workplan_id', 'employee_id'], ForeignKeyValidator::class ],
            [ ['type', 'status', 'ehr_id', 'employee_id'], 'required' ],
//            [ ['datetime'],
//                'unique',
//                'filter' => function (ActiveQuery $query) {
//                    return $query
//                        ->notDeleted()
//                        ->andWhere([
//                            '<>',
//                            Attendance::tableColumns('status'),
//                            Attendance::STATUS_CANCEL
//                        ])
//                        ->andWhere([
//                            '<>',
//                            Attendance::tableColumns('status'),
//                            Attendance::STATUS_INVALID
//                        ]);
//                },
//                'targetAttribute' => ['employee_id', 'ehr_id', 'datetime', 'type', 'status'],
//                'message' => 'Такая запись уже существует.',
//            ],
//            [ 'datetime', 'date', 'format' => CommonHelper::FORMAT_DATETIME_UI ],
        ];
    }
}
