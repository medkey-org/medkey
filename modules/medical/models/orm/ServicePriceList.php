<?php
namespace app\modules\medical\models\orm;

use app\common\db\ActiveRecord;
use app\common\helpers\CommonHelper;
use app\modules\medical\MedicalModule;

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
            self::STATUS_ACTIVE => MedicalModule::t('servicePriceList', 'Active'),
            self::STATUS_INACTIVE => MedicalModule::t('servicePriceList', 'Inactive'),
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
            'name' => MedicalModule::t('servicePriceList', 'Name'),
            'status' => MedicalModule::t('servicePriceList','Status'),
            'start_date' => MedicalModule::t('servicePriceList','Start date'),
            'end_date' => MedicalModule::t('servicePriceList','End date'),
        ];
    }
}
