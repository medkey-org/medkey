<?php
namespace app\common\mail;

/**
 * Class EmailSenderService
 * @package Common\Mail
 * @copyright 2012-2019 Medkey
 */
class EmailSenderService implements EmailSenderServiceInterface
{
    /**
     * @inheritdoc
     */
    public function sendMessageEmail($to, $subject, $content)
    {
        $check = false;
        try {
            $sender = \Yii::$app->mailer
                ->compose()
                ->setFrom(getenv('NOTIFICATION_MAILER_USERNAME'))
                ->setTo($to)
                ->setTextBody($content)
                ->setSubject($subject);
            $check = $sender->send();
            if ($check) {
                \Yii::info("NotificationEmailSender:: Success send email : " . print_r($to, true), 'email');
            } else {
                \Yii::info("NotificationEmailSender:: Failed send email : " . $sender->toString(), 'email');
            }
        } catch (\Exception $e) {
            \Yii::error("NotificationEmailSender:: Error message: {$e->getMessage()}, file : {$e->getFile()}, line : {$e->getLine()}", 'email');
        }
        return $check;
    }
}
