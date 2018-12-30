<?php
namespace app\common\notification;

/**
 * Interface NotificationServiceInterface
 * @package Common\Notification
 * @copyright 2012-2019 Medkey
 */
interface NotificationServiceInterface
{
    public function save($message, $type, $to);
    public function sendAll($type);
}
