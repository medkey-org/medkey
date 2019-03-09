<?php
namespace app\commands;

use app\common\console\Controller;
use app\common\notification\models\Notification;
use app\common\notification\NotificationServiceInterface;

/**
 * Class SkypeBotController
 * @package Common\CLI
 * @copyright 2012-2019 Medkey
 */
class NotificationController extends Controller
{
    public $notificationService;

    public function __construct(
        $id,
        $module,
        NotificationServiceInterface $notificationService,
        array $config = []
    ) {
        $this->notificationService = $notificationService;
        parent::__construct($id, $module, $config);
    }

    public function actionAllExecute()
    {
        $this->notificationService->sendAll(Notification::TYPE_MAIL);
        $this->notificationService->sendAll(Notification::TYPE_SKYPE);
    }

    public function actionMailExecute()
    {

    }

    public function actionViberExecute()
    {

    }

    public function actionWebPushExecute()
    {

    }
}
