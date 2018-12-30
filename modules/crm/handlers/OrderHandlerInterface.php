<?php
namespace app\modules\crm\handlers;

use Symfony\Component\Workflow\Event\Event;

/**
 * Interface OrderStateSubscriberInterface
 * @package Module\CRM
 * @copyright 2012-2019 Medkey
 */
interface OrderHandlerInterface
{
    public function onPaid(Event $e);
}
