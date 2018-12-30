<?php
namespace app\common\base;

/**
 * Class Formatter
 * @package Common\Base
 * @copyright 2012-2019 Medkey
 */
class Formatter extends \yii\i18n\Formatter
{
    /**
     * @var string
     */
    public $nullDisplay = '';
    /**
     * @var int
     */
    public $sizeFormatBase = 1024;
}
