<?php
namespace app\common\widgets;

/**
 * Class TimePicker
 * @package Common\Widgets
 * @copyright 2012-2019 Medkey
 *
 */
class TimePicker extends \kartik\time\TimePicker
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->pluginOptions['showMeridian'] = false;
        parent::init();
    }
}
