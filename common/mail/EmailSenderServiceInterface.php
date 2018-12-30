<?php
namespace app\common\mail;

/**
 * Interface EmailSenderServiceInterface
 * @package Common\Mail
 * @copyright 2012-2019 Medkey
 */
interface EmailSenderServiceInterface
{
    /**
     * @param $to
     * @param $subject
     * @param $content
     * @return mixed
     */
    public function sendMessageEmail($to, $subject, $content);
}
