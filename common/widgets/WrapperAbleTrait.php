<?php
namespace app\common\widgets;

/**
 * Class WrapperTrait
 * @package Common\Widgets
 * @copyright 2012-2019 Medkey
 *
 */
trait WrapperAbleTrait
{
    /**
     * @var bool
     */
    public $wrapper = false;


    /**
     * @return array
     */
    public function wrapperOptions()
    {
        return [];
    }
}
