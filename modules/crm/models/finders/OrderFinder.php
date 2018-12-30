<?php
namespace app\modules\crm\models\finders;

use app\common\db\OrmFinder;
use app\modules\crm\models\orm\Order;
use app\modules\medical\models\orm\Ehr;

/**
 * Class OrderFinder
 * @package Module\CRM
 * @copyright 2012-2019 Medkey
 */
class OrderFinder extends OrmFinder
{
    /**
     * @var string
     */
    public $ehrId;
    /**
     * @var string
     */
    public $number;
    /**
     * @var string
     */
    public $locationCode;
    /**
     * @var integer
     */
    public $currencySum;
    /**
     * @var integer
     */
    public $status;


    /**
     * @inheritdoc
     */
    public function initCondition()
    {
        $this->query
            ->distinct(true)
            ->joinWith(['location'])
            ->andFilterWhere([
                'location.code' => $this->locationCode
            ])
            ->andFilterWhere([
                'status' => $this->status
            ])
            ->andFilterWhere([
                'currency_sum' => $this->currencySum
            ])
            ->andFilterWhere([
                'ehr_id' => $this->ehrId
            ])
            ->andFilterWhere([
                'like',
                Order::tableColumns('number'),
                $this->number
            ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ ['status'], 'integer', 'on' => 'search' ],
            [ ['number', 'memberName', 'locationCode', 'currencySum'], 'string', 'on' => 'search' ],

        ];
    }
}
