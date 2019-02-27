<?php
namespace app\modules\medical\models\orm;

use app\common\db\ActiveQuery;
use app\common\db\ActiveRecord;
use app\common\db\ResponsibilityEntityInterface;
use app\common\helpers\CommonHelper;
use app\common\validators\ForeignKeyValidator;
use app\modules\medical\MedicalModule;
use app\modules\organization\models\orm\Employee;
use app\modules\workplan\models\orm\Workplan;

/**
 * Class Attendance
 *
 * @property int $status
 * @property int $type
 *
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class Attendance extends ActiveRecord implements ResponsibilityEntityInterface
{
    const STATUS_INVALID = 0; // blue
    const STATUS_NEW = 1; // red
    const STATUS_PROGRESS = 2; // red
    const STATUS_ARCHIVE = 3; // red
    const STATUS_CANCEL = 4; // blue
    const STATUS_CONFIRM = 5; // Waiting for confirmation // red
    const STATUS_ABSENCE = 6; // Missed visit // red
    /**
     * Attendance types
     */
    const TYPE_INVALID = 0;
    const TYPE_TIME = 1;
    const TYPE_QUEUE = 2;
    const TYPE_HOSPITAL = 7; // @todo очень сомнительно хранить такой тип здесь, подумать потом
    const ATTENDANCE_DURATION = 1800; // seconds


    public function getViaResponsibility()
    {
        return $this->hasMany(ResAttendance::class, ['attendance_id' => 'id']);
    }

    public function getResponsibilities()
    {
        return $this->hasMany(Employee::class, ['id' => 'employee_id'])
            ->via('viaResponsibility');
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
            [ 'type', 'default', 'value' => static::TYPE_TIME, ],
            [ 'status', 'default', 'value' => static::STATUS_NEW, ],
            [ ['type', 'status'], 'integer' ],
            [ ['ehr_id', 'workplan_id', 'employee_id'], ForeignKeyValidator::class, ],
            [ ['type', 'status', 'ehr_id', 'employee_id'], 'required' ],
            [ ['datetime'],
                'unique',
                'filter' => function (ActiveQuery $query) {
                    return $query
                        ->notDeleted()
                        ->andWhere([
                            '<>',
                            Attendance::tableColumns('status'),
                            Attendance::STATUS_CANCEL
                        ])
                        ->andWhere([
                            '<>',
                            Attendance::tableColumns('status'),
                            Attendance::STATUS_INVALID
                        ]);
                },
                'targetAttribute' => ['employee_id', 'ehr_id', 'datetime', 'type', 'status'],
                'message' => MedicalModule::t('attendance', 'Record already exists'),
            ],
//            [ 'datetime', 'date', 'format' => CommonHelper::FORMAT_DATETIME_UI ],
        ];
    }

    /**
     * @return array
     */
    public static function types()
    {
        return [
            self::TYPE_INVALID => 'Инвалидный',
            self::TYPE_TIME => 'По записи',
            self::TYPE_QUEUE => 'По живой очереди',
            self::TYPE_HOSPITAL => 'Госпитализация',
        ];
    }

    /**
     * @return string
     */
    public function typeName()
    {
        $types = $this::types();
        return !isset($types[$this->type]) ? '' : $types[$this->type];
    }

    /**
     * @return array
     */
    public static function statuses()
    {
        return [
            self::STATUS_INVALID => 'Инвалидный',
            self::STATUS_NEW => 'Новый',
            self::STATUS_PROGRESS => 'В процессе',
            self::STATUS_ARCHIVE => 'В архите',
            self::STATUS_CANCEL => 'Отменен',
            self::STATUS_CONFIRM => 'Ждет подтверждения',
            self::STATUS_ABSENCE => 'Пропуск приёма',
        ];
    }

    /**
     * @return string
     */
    public function getStatusName()
    {
        $statuses = $this::statuses();
        return !isset($statuses[$this->status]) ? '' : $statuses[$this->status];
    }

    public function getEhr()
    {
        return $this->hasOne(Ehr::class, ['id' => 'ehr_id']);
    }

    public function getEmployee()
    {
        return $this->hasOne(Employee::class, ['id' => 'employee_id']);
    }

    public function getWorkplan()
    {
        return $this->hasOne(Workplan::class, ['id' => 'workplan_id']);
    }

    public function getReferralToAttendance()
    {
        return $this->hasMany(ReferralToAttendance::class, ['attendance_id' => 'id']);
    }

    public function getReferrals()
    {
        return $this->hasMany(Referral::class, ['id' => 'referral_id'])
            ->via('referralToAttendance');
    }

    public function attributeLabelsOverride()
    {
        return [
            'ehr_id' => MedicalModule::t('attendance','EHR'),
            'workplan_id' => MedicalModule::t('attendance','Workplan'),
            'status' => MedicalModule::t('attendance','Status'),
            'datetime' => MedicalModule::t('attendance','Planned at'),
            'type' => MedicalModule::t('attendance','Type'),
            'number' => MedicalModule::t('attendance','Number'),
            'employee_id' => MedicalModule::t('attendance','Employee'),
        ];
    }
}
