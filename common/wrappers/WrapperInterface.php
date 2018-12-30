<?php
namespace app\common\wrappers;

/**
 * Interface WrapperAbleInterface
 * @package Common\Wrappers
 * @copyright 2012-2019 Medkey
 *
 */
interface WrapperInterface
{
    /**
     * @return mixed
     */
    public function injectWrapperOptions();

    /**
     * @return mixed
     */
    public function renderContent();
}
