<?php
namespace app\common\notification;

use app\common\mail\EmailSenderServiceInterface;
use app\common\notification\models\Notification;
use app\common\service\ApplicationService;
use app\common\service\exception\ApplicationServiceException;
use app\common\bots\SkypeBotServiceInterface;

/**
 * Class NotificationService
 * @package Common\Notification
 * @copyright 2012-2019 Medkey
 */
class NotificationService extends ApplicationService implements NotificationServiceInterface
{
    public $skypeBotService;
    public $emailSenderService;

    public function __construct(
        EmailSenderServiceInterface $emailSenderService,
        SkypeBotServiceInterface $skypeBotService,
        array $config = []
    ) {
        $this->emailSenderService = $emailSenderService;
        $this->skypeBotService = $skypeBotService;
        parent::__construct($config);
    }

    public function save($message, $type, $to, $options = [])
    {
        $notification = new Notification([
            'scenario' => 'create',
        ]);
        $notification->type = $type;
        $notification->status = Notification::STATUS_NOT_SENT;
        $notification->message = $message;
        $notification->to = $to;
        if (!empty($options)) {
            foreach ($options as $key => $option) {
                if (!$notification->hasProperty($key)) {
                    continue;
                }
                $notification->{$key} = $option;
            }
        }
        if (!$notification->save()) {
            throw new ApplicationServiceException('Can\'t create notification.');
        }
        return true;
    }

    public function sendAll($type)
    {
        $query = Notification::find()->where([
            'type' => $type,
            'status' => Notification::STATUS_NOT_SENT
        ])->all();

        switch ($type) {
            case Notification::TYPE_MAIL:
                return $this->sendByMail($query);
            case Notification::TYPE_SKYPE:
                return $this->sendBySkype($query);
            case Notification::TYPE_WEBPUSH:
                return '';
            default:
                throw new ApplicationServiceException("Unsupported notification type.");
        }
    }

    private function sendByMail($notifications)
    {
        $subject = "[" . getenv('NOTIFICATION_GREETING') . "] [Activity] Notification since " . date('d.m.Y H:i:s');
        $toes = $this->getToes($notifications);
        foreach ($toes as $to => $options) {
            $content = $this->getContent($notifications, $to);
            $this->emailSenderService->sendMessageEmail($to, $subject, $content);
        }
    }

    private function sendBySkype($notifications)
    {
        $toes = $this->getToes($notifications);
        foreach ($toes as $to => $options) {
            $content = $this->getContent($notifications, $to);
            $this->skypeBotService->sendMessageWithoutRequest($to, $content, $options['skype_service_url']);
        }
    }

    /**
     * @param $notifications
     * @return array
     */
    private function getToes($notifications)
    {
        $toes = [];
        foreach ($notifications as $to) {
            if (is_null($to->to)) {
                continue;
            }
            // prepare options
            if ($to->type === Notification::TYPE_MAIL) {
                $toes[$to->to] = [];
            } elseif ($to->type === Notification::TYPE_SKYPE) {
                $toes[$to->to] = [
                    'skype_service_url' => $to->skype_service_url
                ];
            }
        }
        return $toes;
    }

    /**
     * @param $notifications
     * @param $to
     * @return string
     */
    private function getContent($notifications, $to)
    {
        $content = "== Detected activity ==" . PHP_EOL;
        foreach ($notifications as $notification) {
            if ($to === $notification->to) {
                $content .= $notification->message;
                $content .= PHP_EOL;
                $notification->status = Notification::STATUS_SENT;
                $notification->save();
            }
        }

        return $content;
    }
}
