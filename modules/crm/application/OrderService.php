<?php
namespace app\modules\crm\application;

use app\common\base\Model;
use app\common\data\ActiveDataProvider;
use app\common\db\ActiveRecord;
use app\common\db\Exception;
use app\common\db\Query;
use app\common\helpers\ArrayHelper;
use app\common\service\exception\ApplicationServiceException;
use app\modules\crm\CrmModule;
use app\modules\crm\models\form\OrderItem as OrderItemForm;
use app\common\dto\Dto;
use app\common\service\ApplicationService;
use app\common\service\exception\AccessApplicationServiceException;
use app\modules\config\entities\CurrencyEntity;
use app\modules\crm\models\finders\OrderFinder;
use app\modules\crm\models\orm\Order;
use app\modules\crm\models\orm\OrderItem;
use app\modules\location\models\orm\Location;
use app\modules\crm\models\form\Order as OrderForm;

/**
 * Class OrderService
 * @package Module\CRM
 * @copyright 2012-2019 Medkey
 */
class OrderService extends ApplicationService implements OrderServiceInterface
{
    /**
     * @inheritdoc
     */
    public function getPrivileges()
    {
        return [
            'getOrderList' => CrmModule::t('order', 'Get order list'),
            'getOrderCountForWeek' => CrmModule::t('order', 'Order count for week'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function aclAlias()
    {
        return CrmModule::t('order', 'Order');
    }

    /**
     * @inheritdoc
     */
    public function getOrderCountForWeek()
    {
        /** @var $form OrderFinder */
        if (!$this->isAllowed('getOrderCountForWeek')) {
            throw new AccessApplicationServiceException('Доступ запрещен.');
        }
        return (new Query)
            ->from(Order::tableName())
            ->select(['DATE(created_at) AS created_at_day', 'COUNT(id)'])
            // @todo Add week selection
            ->groupBy(['created_at_day'])
            ->orderBy('created_at_day')
            ->all();
    }

    public function getOrderItemList($form)
    {
        $query = OrderItem::find();
        $query
            ->andFilterWhere([
                'order_id' => $form->orderId,
            ]);
        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getOrderList(Model $form)
    {
        /** @var $form OrderFinder */
        if (!$this->isAllowed('getOrderList')) {
            throw new AccessApplicationServiceException('Доступ запрещен.');
        }
        $query = Order::find();
        $query
            ->distinct(true)
            ->joinWith(['location'])
            ->andFilterWhere([
                Location::tableColumns('code') => $form->locationCode
            ])
            ->andFilterWhere([
                Order::tableColumns('status') => $form->status
            ])
            ->andFilterWhere([
                Order::tableColumns('currency_sum') =>
                    empty($form->currencySum) ? null : CurrencyEntity::moneyEncode($form->currencySum, 'RUB'),
            ])
            ->andFilterWhere([
                Order::tableColumns('ehr_id') => $form->ehrId
            ])
            ->andFilterWhere([
                'like',
                Order::tableColumns('number'),
                $form->number
            ]);
        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10
            ],
        ]);
    }

    public function createOrderItem($form)
    {
        if (!$form instanceof OrderItemForm) {
            throw new ApplicationServiceException('Error create order item');
        }
        $order = Order::findOneEx($form->order_id);
        $model = new OrderItem(['scenario' => 'create']);
        $model->loadForm($form);
        $model->currency = $order->currency;
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            if (!$model->save()) {
                throw new ApplicationServiceException('Error create order item');
            }
            $this->recalculationOrder($order->id);
            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
        return $model;
    }

    public function updateOrderItem($id, $form)
    {
        if (!$form instanceof OrderItemForm) {
            throw new ApplicationServiceException('Error update order item');
        }
        $order = Order::findOneEx($form->order_id);
        $model = OrderItem::findOneEx($id);
        $model->loadForm($form);
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            if (!$model->save()) {
                throw new ApplicationServiceException('Error update order item');
            }
            $this->recalculationOrder($order->id);
            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
        return $model;
    }

    public function getOrderById($orderId)
    {
        return Order::findOneEx($orderId);
    }

    /**
     * @todo выделить в отдельный процесс (реестр процессов...) или в ORM
     * @param string $orderId
     * @return bool
     * @throws Exception
     */
    protected function recalculationOrder($orderId)
    {
        $order = Order::findOneEx($orderId);
        if (empty($order->orderItems)) {
            return false;
        }
        $finalCurrencySum = 0;
        $currencySum = 0;
        foreach ($order->orderItems as $item) {
            $finalCurrencySum += $item->final_currency_sum;
            $currencySum += $item->currency_sum;
        }
        $order->final_currency_sum = $finalCurrencySum;
        $order->currency_sum = $currencySum;
        if (!$order->save()) {
            return false;
        }
        return true;
    }

    public function getOrderForm($raw)
    {
        $model = Order::ensureWeak($raw);
        $orderForm = new OrderForm();
        if ($model->isNewRecord) {
            $orderForm->setScenario('create');
        }
        $orderForm->loadAr($model);
        $orderForm->id = $model->id;
        $orderForm->ehr = ArrayHelper::toArray($model->ehr);
        return $orderForm;
    }

    public function getOrderItemForm($raw, $scenario = 'create')
    {
        $model = OrderItem::ensureWeak($raw);
        $orderItemForm = new OrderItemForm([
        ]);
        $orderItemForm->setScenario($scenario);
        $orderItemForm->loadAr($model);
        $orderItemForm->id = $model->id;
        return $orderItemForm;
    }

//    public function recalculationItem($orderItemId, $discountPoint = 0, $discountPrice = 0)
//    {
////        // todo блокировать запись
////        // todo SELECT .... FOR UPDATE,
////        // todo чтобы конкурентные транзакции ждали
////        // todo пока выполнится эта транзакция, в которой есть данный SELECT
////        $transaction = \Yii::$app->db->beginTransaction();
////        try {
////            $orderItem = OrderItem::findOneEx($orderItemId);
////            $orderItem->discount_point = $discountPoint;
////            $orderItem->discount_price = $discountPrice;
////            $orderItem->final_currency_sum -= $discountPoint;
////            $orderItem->final_currency_sum -= $discountPrice;
////            $order = Order::findOneEx($orderItem->order_id);
////            $order->final_currency_sum -= $discountPoint;
////            $order->final_currency_sum -= $discountPrice;
////            $order->save();
////            $orderItem->save();
////            $transaction->commit();
////        } catch (Exception $e) {
////            $transaction->rollBack();
////            throw $e;
////        }
//    }

    /**
     * @todo сделать этот метод аля save() или просто добавить еще один, смотри задачу #177
     * @param Dto $orderDto
     * @param string $scenario
     * @return ActiveRecord
     * @throws \Exception
//     */
//    public function add($orderDto, $scenario = ActiveRecord::SCENARIO_CREATE)
//    {
//        $modelClass = $this->modelClass;
//        if (!($orderDto instanceof Dto)) {
//            throw new InvalidValueException('Object is not instance Dto class (OrderDto)'); // todo normalize text
//        }
//        $transaction = \Yii::$app->db->beginTransaction();
//        try {
//            /** @var Order $model */
//            $model = new $modelClass([
//                'scenario' => $scenario
//            ]);
//            $model->loadDto($orderDto);
//            if (!$model->save()) {
//                $errors = Json::encode($model->getErrors());
//                throw new \DomainException('Не удалось сохранить заказ. Причина: ' . $errors); // todo выводить ошибки валидации
//            }
//            if (!empty($orderDto->orderItems)) {
//                $this->saveOrderItems($model->id, $orderDto->orderItems, $scenario);
//            }
//            $transaction->commit();
//        } catch (\Exception $e) {
//            $transaction->rollBack();
//            throw $e;
//        }
//        return $model;
//    }

    /**
     * @todo link/unlink AR
     * @param string $orderId
     * @param array $orderItems
     * @param string $scenario
     * @return null
//     * @throws Exception
//     */
//    public function saveOrderItems($orderId, $orderItems, $scenario)
//    {
//        $order = Order::findOneEx($orderId);
//        $q = OrderItem::find()
//            ->where([
//                'order_id' => $order->id
//            ])
//            ->notDeleted();
//        $exists = $q->all();
//        foreach ($exists as $e) {
//            $e->delete();
//        }
//        if (!is_array($orderItems)) {
//            return null;
//        }
//        $itemNumber = 1; // todo возможно нужен в валидатор, но так быстрее
//        foreach ($orderItems as $orderItem) {
//            $model = new OrderItem([
//                'scenario' => $scenario
//            ]);
//            $model->setAttributes($orderItem);
//            $model->order_id = $order->id;
//            $model->item_number = $itemNumber;
//            $model->currency = $order->currency;
//            if (!$model->save()) {
//                $errors = Json::encode($model->getErrors());
//                throw new \DomainException('Не удалось сохранить позицию заказа. Причина: ' . $errors); // todo выводить ошибки валидации
//            }
//            $itemNumber++;
//        }
//    }
}
