<?php
namespace app\modules\crm\models\form;

use app\common\base\Model;
use app\common\validators\ForeignKeyValidator;
use app\modules\config\entities\CurrencyEntity;
use app\modules\crm\CrmModule;
use app\modules\crm\models\orm\Order as OrderOrm;

/**
 * Class Order
 * @package Module\CRM
 * @copyright 2012-2019 Medkey
 */
class Order extends Model
{
    public $id;
    public $number;
    public $status;
    public $ehr_id;
    public $currency;
    public $currency_sum;
    public $final_currency_sum;
    public $type;
    public $location_id;
    public $description;
    public $ehr;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ [ 'currency', 'status', 'ehr_id' ], 'required' ],
//            [ ['number'], 'default', 'value' => function () {
//                return $this->generateNumber();
//            } ],
            [ ['number', 'currency', 'location_id', 'description'],
                'string',
            ],
            [ ['ehr_id', 'location_id'], ForeignKeyValidator::class ],
            [ 'status', 'integer' ],
            [ ['currency_sum', 'final_currency_sum'],
                'double',
            ],
            [ ['currency_sum', 'final_currency_sum'], 'default', 'value' => '0.00' ],
            [ ['currency_sum'], 'filter',
                'filter' => function () {
                    if (empty($this->currency)) {
                        $this->addError('currency', 'Необходимо заполнить ' . $this->getAttributeLabel('currency') . '.');
                    } else {
                        if (!is_string($this->currency_sum)) {
                            $this->currency_sum = (string)$this->currency_sum;
                        }
                        return CurrencyEntity::moneyEncode($this->currency_sum, $this->currency);
                    }
                },
            ],
            [ ['final_currency_sum'], 'filter',
                'filter' => function () {
                    if (empty($this->currency)) {
                        $this->addError('currency', 'Необходимо заполнить ' . $this->getAttributeLabel('currency') . '.');
                    } else {
                        if (!is_string($this->final_currency_sum)) {
                            $this->final_currency_sum = (string)$this->final_currency_sum;
                        }
                        return CurrencyEntity::moneyEncode($this->final_currency_sum, $this->currency);
                    }
                },
            ],
            [ ['number'], 'required' ],
            [ ['number'],
                'unique',
                'targetClass' => \app\modules\crm\models\orm\Order::class,
                'targetAttribute' => ['number'],
                'filter' => function ($query) {
                    return $query
                        ->andFilterWhere([
                            '<>',
                            'id',
                            $this->id
                        ])
                        ->notDeleted();
                },
            ],
        ];
    }

    /**
     * @return string
     */
    public function getStatusName()
    {
        $statuses = OrderOrm::statuses();
        return isset($statuses[$this->status]) ? $statuses[$this->status] : null;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'number' => CrmModule::t('order', 'Number'),
            'status' => CrmModule::t('order', 'Status'),
            'ehr_id' => CrmModule::t('order', 'EHR'),
            'currency' => CrmModule::t('order', 'Currency'),
            'currency_sum' => CrmModule::t('order', 'Amount'),
            'final_currency_sum' => CrmModule::t('order', 'Final amount'),
            'type' => CrmModule::t('order', 'Type'),
            'location_id' => CrmModule::t('order', 'Location'),
            'description' => CrmModule::t('order', 'Description'),
        ];
    }
}
