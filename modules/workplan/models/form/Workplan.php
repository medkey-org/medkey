<?php
namespace app\modules\workplan\models\form;

use app\common\base\Model;
use app\common\helpers\CommonHelper;
use app\common\validators\ForeignKeyValidator;
use app\modules\workplan\WorkplanModule;

/**
 * Class Workplan
 * @package Module\Workplan
 * @copyright 2012-2019 Medkey
 */
class Workplan extends Model
{
    const WEEK_MONDAY = 1;
    const WEEK_TUESDAY = 2;
    const WEEK_WEDNESDAY = 3;
    const WEEK_THURSDAY = 4;
    const WEEK_FRIDAY = 5;
    const WEEK_SATURDAY = 6;
    const WEEK_SUNDAY = 7;

    public $id;
    public $since_date;
    public $expire_date;
    public $since_time;
    public $expire_time;
    public $rules;
    public $employee_id;
    public $cabinet_id;
    public $department_id;
    public $status;
    public $weekIds; // контейнер из релейшна

    // relations
    public $employee;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (!empty($this->expire_date)) {
            $this->expire_date = \Yii::$app->formatter->asDate($this->expire_date, CommonHelper::FORMAT_DATE_UI);
        }
        if (!empty($this->since_date)) {
            $this->since_date = \Yii::$app->formatter->asDate($this->since_date, CommonHelper::FORMAT_DATE_UI);
        }
    }

    /**
     * @return array
     */
    public static function listWeek()
    {
        return [
            self::WEEK_MONDAY => \Yii::t('app', 'Monday'),
            self::WEEK_TUESDAY => \Yii::t('app', 'Tuesday'),
            self::WEEK_WEDNESDAY => \Yii::t('app', 'Wednesday'),
            self::WEEK_THURSDAY => \Yii::t('app', 'Thursday'),
            self::WEEK_FRIDAY => \Yii::t('app', 'Friday'),
            self::WEEK_SATURDAY => \Yii::t('app', 'Saturday'),
            self::WEEK_SUNDAY => \Yii::t('app', 'Sunday')
        ];
    }

    /**
     * @param int $key
     * @return string|null
     */
    public static function getWeekName($key)
    {
        $list = static::listWeek();
        if (isset($list[$key])) {
            return $list[$key];
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ 'weekIds', 'required', ],
            [ ['since_date', 'expire_date', 'since_time', 'expire_time', 'cabinet_id',], 'required', ],
            [ 'weekIds', 'safe' ], // todo each validator
            [ ['since_date', 'expire_date'], 'date', 'format' => CommonHelper::FORMAT_DATE_UI ],
            [ ['since_time', 'expire_time'], 'date', 'format' => CommonHelper::FORMAT_TIME_UI ],
            [ ['employee_id', 'cabinet_id', 'department_id',], ForeignKeyValidator::class ],
            [ ['status'], 'integer' ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
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
