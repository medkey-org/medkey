<?php
namespace app\modules\crm\models\orm;

use app\common\db\ActiveRecord;
use app\common\validators\ForeignKeyValidator;
use app\modules\config\entities\CurrencyEntity;
use app\modules\crm\CrmModule;
use app\modules\medical\models\orm\Service;

/**
 * Class OrderItem
 *
 * @property string $order_id
 * @property int $item_number
 * @property int $type_transaction
 * @property string $currency
 * @property int $discount_point
 * @property int $discount_price
 * @property int $final_currency_sum
 * @property int $currency_sum
 * @property string $base_accrual
 * $property string $base_redemption
 *
 * @package Module\CRM
 * @copyright 2012-2019 Medkey
 */
class OrderItem extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        if (!empty($this->currency) && !empty($this->currency_sum)) {
            $this->currency_sum = CurrencyEntity::moneyDecode($this->currency_sum, $this->currency);
        }
//        if (!empty($this->currency) && !empty($this->currency_sum_per_unit)) {
//            $this->currency_sum_per_unit = CurrencyEntity::moneyDecode($this->currency_sum_per_unit, $this->currency);
//        }
        if (!empty($this->currency) && !empty($this->final_currency_sum)) {
            $this->final_currency_sum = CurrencyEntity::moneyDecode($this->final_currency_sum, $this->currency);
        }
        parent::afterFind();
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        if (!empty($this->currency) && !empty($this->currency_sum)) {
            $this->currency_sum = CurrencyEntity::moneyDecode($this->currency_sum, $this->currency);
        }
//        if (!empty($this->currency) && !empty($this->currency_sum_per_unit)) {
//            $this->currency_sum_per_unit = CurrencyEntity::moneyDecode($this->currency_sum_per_unit, $this->currency);
//        }
        if (!empty($this->currency) && !empty($this->final_currency_sum)) {
            $this->final_currency_sum = CurrencyEntity::moneyDecode($this->final_currency_sum, $this->currency);
        }
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        if (!empty($this->currency) && !empty($this->currency_sum)) {
            $this->currency_sum = CurrencyEntity::moneyEncode($this->currency_sum, $this->currency);
        }
//        if (!empty($this->currency) && !empty($this->currency_sum_per_unit)) {
//            $this->currency_sum_per_unit = CurrencyEntity::moneyEncode($this->currency_sum_per_unit, $this->currency);
//        }
        if (!empty($this->currency) && !empty($this->final_currency_sum)) {
            $this->final_currency_sum = CurrencyEntity::moneyEncode($this->final_currency_sum, $this->currency);
        }
        return true;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }

    /**
     * @todo межмодульная связь по БД
     * @return \yii\db\ActiveQuery
     */
    public function getService()
    {
        return $this->hasOne(Service::class, ['id' => 'service_id']);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ ['order_id', 'currency', 'service_id', 'currency_sum', 'final_currency_sum' ],
                'required',
                'on' => [ActiveRecord::SCENARIO_CREATE, ActiveRecord::SCENARIO_UPDATE]
            ],
            [ ['order_id', 'service_id'], ForeignKeyValidator::class ],
            [ 'qty', 'default', 'value' => 1 ],
            [ ['item_number', 'qty'],
                'integer',
                'on' => [ActiveRecord::SCENARIO_CREATE, ActiveRecord::SCENARIO_UPDATE]
            ],
            [ ['currency'],
                'string',
                'on' => [ActiveRecord::SCENARIO_CREATE, ActiveRecord::SCENARIO_UPDATE]
            ],
            [ ['item_number'],
                'validateUnique',
                'on' => ['create'],
            ],
            [ ['item_number'],
                'default',
                'value' => function ($model) {
                    $count = OrderItem::find()
                        ->where(['order_id' => $model->order_id])
                        ->notDeleted()
                        ->count();
                    if ((int)$count === 0) {
                        return 1;
                    }
                    return ++$count;
                }, 'on' => [ActiveRecord::SCENARIO_CREATE]
            ],
            [ ['currency_sum', 'final_currency_sum'],
                'double',
                'on' => [ActiveRecord::SCENARIO_CREATE, ActiveRecord::SCENARIO_UPDATE]
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
        $orderItem = static::find()
            ->where([
                'order_id' => $this->order_id,
                'item_number' => $this->item_number,
            ])
            ->notDeleted()
            ->one();
        if (!isset($orderItem)) {
            $this->addError($attribute,  CrmModule::t('order','Given position number already exists'));
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabelsOverride()
    {
        return [
            'item_number' => CrmModule::t('order', 'Position number'),
            'currency_sum' => CrmModule::t('order', 'Amount'),
            'currency' => CrmModule::t('order', 'Currency'),
            'order_id' => CrmModule::t('order', 'Order'),
//            'currency_sum_per_unit' => 'Сумма в валюте за единицу',
            'qty' => CrmModule::t('order', 'Quantity'),
            'final_currency_sum' => CrmModule::t('order', 'Final amount'),
            'discount_point' => CrmModule::t('order', 'Discount by point'),
            'discount_currency_sum' => CrmModule::t('order', 'Discount amount'),
            'service_id' => CrmModule::t('order', 'Service'),
        ];
    }
}
