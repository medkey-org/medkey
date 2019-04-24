<?php
namespace app\modules\medical\models\form;

use app\common\base\Model;
use app\common\helpers\CommonHelper;
use app\modules\medical\MedicalModule;
use app\modules\medical\models\orm\ServicePriceList as ServicePriceListORM;

/**
 * Class ServicePriceList
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class ServicePriceList extends Model
{
    public $id;
    public $name;
    public $status;
    public $start_date;
    public $end_date;
    public $currency;

    /**
     * @return string
     */
    public function getStatusName()
    {
        $statuses = ServicePriceListORM::statuses();
        if (!isset($statuses[$this->status])) {
            return '';
        }
        return $statuses[$this->status];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ ['name', 'currency', 'status', 'start_date', 'end_date'], 'required' ],
            [ ['name', 'currency'], 'string', ],
            [ ['status'], 'integer', ],
            [ ['start_date'], 'filter', 'filter' => function ($value) {
                return \Yii::$app->formatter->asDate($value, CommonHelper::FORMAT_DATE_UI);
            } ],
            [ ['end_date'], 'filter', 'filter' => function ($value) {
                return \Yii::$app->formatter->asDate($value, CommonHelper::FORMAT_DATE_UI);
            } ],
            [ ['start_date', 'end_date'],
                'date',
                'format' => CommonHelper::FORMAT_DATE_UI,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => MedicalModule::t('servicePriceList', 'Name'),
            'status' => MedicalModule::t('servicePriceList','Status'),
            'start_date' => MedicalModule::t('servicePriceList','Start date'),
            'end_date' => MedicalModule::t('servicePriceList','End date'),
        ];
    }
}
