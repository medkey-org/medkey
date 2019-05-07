<?php
namespace app\modules\crm\application;

use app\common\base\Model;
use app\common\service\exception\AccessApplicationServiceException;
use app\modules\crm\models\orm\Order;
use yii\data\DataProviderInterface;

/**
 * Interface OrderServiceInterface
 * @package Module\CRM
 * @copyright 2012-2019 Medkey
 */
interface OrderServiceInterface
{
    /**
     * @param int $orderItemId
     * @param int $discountPoint
     * @param int $discountPrice
     * @return void
     */
//    public function recalculationItem($orderItemId, $discountPoint = 0, $discountPrice = 0);

    /**
     * @param Model $form
     * @return DataProviderInterface
     * @throws AccessApplicationServiceException
     */
    public function getOrderList(Model $form);

    /**
     * Get orders count for week
     * @return DataProviderInterface
     * @throws AccessApplicationServiceException
     * @todo Add week selection
     */
    public function getOrderCountForWeek();

    /**
     * @param string $orderId
     * @return Order
     */
    public function getOrderById($orderId);
    public function getOrderItemList($form);
    public function getOrderItemForm($raw, $scenario = 'create');
    public function updateOrderItem($id, $form);
    public function createOrderItem($form);
}
