<?php
namespace app\modules\location\models\orm;

use app\common\db\ActiveRecord;
use app\common\helpers\CommonHelper;
use app\modules\location\LocationModule;

/**
 * Class Location
 *
 * @property string $description
 * @property integer $code
 * @property integer $status
 * @property string $start_date
 * @property string $end_date
 *
 * @package Module\Location
 * @copyright 2012-2019 Medkey
 */
class Location extends ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;
    const STATUS_ACTIVE_NAME = 'Active';
    const STATUS_INACTIVE_NAME = 'Inactive';


    /**
     * @return array
     */
    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE => LocationModule::t('common', self::STATUS_ACTIVE_NAME),
            self::STATUS_INACTIVE => LocationModule::t('common', self::STATUS_INACTIVE_NAME),
        ];
    }

    /**
     * @return string
     */
    public function getStatusName()
    {
        $types  = $this::statuses();

        return !empty($types[$this->status]) ? $types[$this->status] : '';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ ['code', 'start_date', 'end_date'],
                'required',
                'on' => [ActiveRecord::SCENARIO_CREATE, ActiveRecord::SCENARIO_UPDATE]
            ],
            [ ['code', 'status'],
                'integer',
                'on' => [ActiveRecord::SCENARIO_CREATE, ActiveRecord::SCENARIO_UPDATE]
            ],
            [ ['description'],
                'string',
                'on' => [ActiveRecord::SCENARIO_CREATE, ActiveRecord::SCENARIO_UPDATE]
            ],
            [ 'start_date', 'filter', 'filter' => function () {
                return $this->start_date = \Yii::$app->formatter->asDate($this->start_date, CommonHelper::FORMAT_DATE_DB);
            }, 'on' => [ActiveRecord::SCENARIO_CREATE, ActiveRecord::SCENARIO_UPDATE]
            ],
            [ 'end_date', 'filter', 'filter' => function () {
                return $this->end_date = \Yii::$app->formatter->asDate($this->end_date, CommonHelper::FORMAT_DATE_DB);
            }, 'on' => [ActiveRecord::SCENARIO_CREATE, ActiveRecord::SCENARIO_UPDATE]
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabelsOverride()
    {
        return [
            'code' => LocationModule::t('common', 'Code'),
            'status' => LocationModule::t('common', 'Status'),
            'member_id' => LocationModule::t('common', 'Member'),
            'start_date' => LocationModule::t('common', 'Start date'),
            'end_date' => LocationModule::t('common', 'End date'),
        ];
    }
}
