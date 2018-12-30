<?php
namespace app\modules\crm\handlers;

use app\common\dto\Dto;
use app\modules\crm\models\orm\Order;
use app\modules\medical\application\ReferralServiceInterface;
use Symfony\Component\Workflow\Event\Event;

/**
 * Class OrderStateSubscriber
 * @package Module\CRM
 * @copyright 2012-2019 Medkey
 */
class OrderHandler implements OrderHandlerInterface
{
    public function onPaid(Event $e)
    {
        /** @var Order $order */
        $order = $e->getSubject();
        $o = $order->toArray([], ['orderItems']);
        $orderDto = Dto::make($o);
        /** @var ReferralServiceInterface $referralAppService */
        $referralAppService = \Yii::$container->get(ReferralServiceInterface::class);
        $referralAppService->generateReferralByOrder($orderDto);
    }
}
