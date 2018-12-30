<?php
namespace app\common\wrappers;

/**
 * Class WrapperAbleTrait
 * @package Common\Wrappers
 * @copyright 2012-2019 Medkey
 */
trait WrapperTrait
{
    /**
     * @var string
     */
    public $wrapperContent;


    /**
     * @return null|void
     */
    public function injectWrapperOptions()
    {
        if (!isset($this->wrapperOptions) || !is_array($this->wrapperOptions)) {
            return null;
        }
        foreach ($this->wrapperOptions as $key => $option) {
            !property_exists($this, $key) ?: $this->{$key} = $option;
        }
    }

    /**
     * @return string
     */
    public function renderContent()
    {
        if (isset($this->wrapperContent) && gettype($this->wrapperContent) === 'string') {
            return $this->wrapperContent;
        } elseif (isset($this->wrapperContent) && gettype($this->wrapperContent) === 'object') {
            // Need to be implemented
        }
    }
}
