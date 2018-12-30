<?php
namespace app\common\widgets;

/**
 * Class Dialog
 * @package Common\Widgets
 * @copyright 2012-2019 Medkey
 *
 */
class Dialog extends \yii\jui\Dialog
{
    use IdWidgetTrait;

    public function init()
    {
        $this->options['id'] = $this->getId();
        parent::init();
    }
}
