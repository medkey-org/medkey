<?php
namespace app\modules\medical\models\orm;

use app\common\db\ActiveRecord;
use app\common\helpers\CommonHelper;

/**
 * Class ServicePriceList
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class ServicePriceList extends ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ ['name', 'currency', 'status', 'start_date', 'end_date'], 'required' ],
            [ ['name', 'currency'], 'string' ],
            [ 'status', 'integer' ],
            [ ['start_date'], 'filter', 'filter' => function ($value) {
                return \Yii::$app->formatter->asDate($value, CommonHelper::FORMAT_DATETIME_DB);
            } ],
            [ ['end_date'], 'filter', 'filter' => function ($value) {
                return \Yii::$app->formatter->asDate($value, CommonHelper::FORMAT_DATETIME_DB);
            } ],
        ];
    }

    /**
     * @return array
     */
    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE => 'Активный',
            self::STATUS_INACTIVE => 'Неактивный',
        ];
    }

    /**
     * @return string
     */
    public function getStatusName()
    {
        $statuses = static::statuses();
        return $statuses[$this->status];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabelsOverride()
    {
        return [
            'name' => 'Имя',
            'status' => 'Статус',
            'start_date' => 'Дата начала',
            'end_date' => 'Дата конца',
        ];
    }
}
