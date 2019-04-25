<?php
namespace app\modules\crm\models\form;

use app\common\base\Model;
use app\common\validators\ForeignKeyValidator;
use app\modules\crm\CrmModule;
use app\modules\crm\models\orm\OrderItem as OrderItemORM;

/**
 * Class Order
 * @package Module\CRM
 * @copyright 2012-2019 Medkey
 */
class OrderItem extends Model
{
    public $id;
    public $order_id;
    public $item_number;
    public $currency;
    public $currency_sum_per_unit;
    public $currency_sum;
    public $final_currency_sum;
    public $discount_point;
    public $discount_currency_sum;
    public $service_id;
    public $qty;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ ['service_id', 'order_id'], ForeignKeyValidator::class, ],
            [ ['order_id', 'currency', 'service_id', 'currency_sum', 'final_currency_sum' ],
                'required',
                'on' => ['create', 'update'],
            ],
            [ 'qty', 'default', 'value' => 1 ],
            [ ['item_number', 'qty'],
                'integer',
                'on' => ['create', 'update'],
            ],
            [ ['currency'],
                'string',
                'on' => ['create', 'update'],
            ],
            [ ['item_number'],
                'validateUnique',
                'on' => ['create'],
            ],
            [ ['item_number'],
                'default',
                'value' => function ($model) {
                    $count = OrderItemORM::find()
                        ->where(['order_id' => $model->order_id])
                        ->notDeleted()
                        ->count();
                    if ((int)$count === 0) {
                        return 1;
                    }
                    return ++$count;
                }, 'on' => 'create'
            ],
            [ ['currency_sum', 'final_currency_sum'],
                'double',
                'on' => ['create', 'update'],
            ],
            [ ['currency_sum', 'final_currency_sum'], 'default', 'value' => '0.00' ],
        ];
    }

    /**
     * @param string $attribute
     * @return void
     */
    public function validateUnique($attribute)
    {
        $orderItem = OrderItemORM::find()
            ->where([
                'order_id' => $this->order_id,
                'item_number' => $this->item_number,
            ])
            ->notDeleted()
            ->one();
        if (!isset($orderItem)) {
            $this->addError($attribute, CrmModule::t('order','Position number already exists'));
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'item_number' => CrmModule::t('order', 'Number'),
            'currency_sum' => CrmModule::t('order', 'Amount'),
            'currency' => CrmModule::t('order', 'Currency'),
            'order_id' => CrmModule::t('order', 'Order'),
//            'currency_sum_per_unit' => 'Сумма в валюте за единицу',
            'qty' => CrmModule::t('order', 'Quantity'),
            'final_currency_sum' => CrmModule::t('order', 'Final amount'),
            'discount_point' => CrmModule::t('order', 'Discount points'),
            'discount_currency_sum' => CrmModule::t('order', 'Discount amount'),
            'service_id' => CrmModule::t('order', 'Service'),
        ];
    }
}
