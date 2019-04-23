<?php
namespace app\modules\crm\models\orm;

use app\common\db\ActiveQuery;
use app\common\db\ActiveRecord;
use app\common\validators\ForeignKeyValidator;
use app\common\workflow\WorkflowEntityInterface;
use app\common\workflow\WorkflowEntityTrait;
use app\modules\config\entities\CurrencyEntity;
use app\modules\crm\CrmModule;
use app\modules\location\models\orm\Location;
use app\modules\medical\models\orm\Ehr;

/**
 * Class Order
 *
 * @property string $number
 * @property string $currency
 * @property int $currency_sum
 * @property int $final_currency_sum
 * @property int $status
 * @property string $card_id
 * @property string $location_id
 * @property-read Location $location
 * @property-read OrderItem[] $orderItems
 *
 * @package Module\CRM
 * @copyright 2012-2019 Medkey
 */
class Order extends ActiveRecord implements WorkflowEntityInterface
{
    const STATUS_NEW = 1;
    const STATUS_PAID = 2;
    const STATUS_ERROR = 3;

    use WorkflowEntityTrait;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->isNewRecord) {
            $this->number = $this->generateNumber();
        }
        if ($this->isNewRecord && $this instanceof WorkflowEntityInterface) {
            $this->status = $this->getStartStatus();
        }
        parent::init();
    }

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->status === self::STATUS_NEW;
    }

    /**
     * @todo number в БД является строкой
     * @todo лучше SEQUENCE на уровне БД делать
     * @return int
     */
    public function generateNumber()
    {
        $db = \Yii::$app->db;
        if ($db->driverName === 'pgsql') {
            $cast = 'cast(number as INT)';
        } elseif ($db->driverName === 'mysql') {
            $cast = 'cast(number as SIGNED)';
        } else {
            $cast = null;
        }
        $max = static::find()
            ->max($cast);
        return (string)++$max;
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        if (!empty($this->currency) && !empty($this->currency_sum)) {
            $this->currency_sum = CurrencyEntity::moneyDecode($this->currency_sum, $this->currency);
        }
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
        if (!empty($this->currency) && !empty($this->final_currency_sum)) {
            $this->final_currency_sum = CurrencyEntity::moneyDecode($this->final_currency_sum, $this->currency);
        }
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ [ 'currency', 'status', 'ehr_id' ], 'required' ],
            [ ['number', 'currency', 'location_id', 'description'],
                'string',
                'on' => [ActiveRecord::SCENARIO_CREATE, ActiveRecord::SCENARIO_UPDATE]
            ],
            [ ['ehr_id', 'location_id'], ForeignKeyValidator::class ],
            [ 'status', 'integer' ],
            [ ['currency_sum', 'final_currency_sum'],
                'double',
                'on' => [ActiveRecord::SCENARIO_CREATE, ActiveRecord::SCENARIO_UPDATE]
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
            [ ['number'], 'unique', 'filter' => function ($query) {
                /** @var $query ActiveQuery */
                return $query
                    ->notDeleted();
            },
                'on' => [ActiveRecord::SCENARIO_CREATE, ActiveRecord::SCENARIO_UPDATE]
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocation()
    {
        return $this->hasOne(Location::class, ['id' => 'location_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEhr()
    {
        return $this->hasOne(Ehr::class, ['id' => 'ehr_id']);
    }

//    /**
//     * @return array
//     */
//    public static function statuses()
//    {
//        return [
//            static::STATUS_NEW => 'Новый',
//            static::STATUS_PAID => 'Оплачен',
//            static::STATUS_ERROR => 'Ошибка'
//        ];
//    }
//
//    /**
//     * @return string
//     */
//    public function getStatusName()
//    {
//        $statuses  = $this::statuses();
//
//        return !empty($statuses[$this->status]) ? $statuses[$this->status] : '';
//    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderItems()
    {
        return $this->hasMany(OrderItem::class, ['order_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabelsOverride()
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

    public function extraFields()
    {
        return [
            'orderItems'
        ];
    }
}
