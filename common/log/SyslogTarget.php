<?php
namespace app\common\log;

use yii\helpers\VarDumper;
use yii\log\Logger;

/**
 * Class SyslogTarget
 * @package Common\Log
 * @copyright 2012-2019 Medkey
 */
class SyslogTarget extends \yii\log\SyslogTarget
{
    /**
     * @var bool
     */
    public $formatted = true;

    /**
     * {@inheritdoc}
     */
    public function formatMessage($message)
    {
        list($text, $level, $category, $timestamp) = $message;
        if (!is_string($text)) {
            // exceptions may not be serializable if in the call stack somewhere is a Closure
            if ($text instanceof \Throwable || $text instanceof \Exception) {
                $text = (string) $text;
            } else {
                $text = VarDumper::export($text);
            }
        }

        if ($this->formatted) {
            $level = Logger::getLevelName($level);
            $prefix = $this->getMessagePrefix($message);
            return "{$prefix}[$level][$category] $text";
        }

        return $text;
    }
}
