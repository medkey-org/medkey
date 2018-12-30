<?php
namespace app\common\widgets;

use app\common\base\InitTrait;

/**
 * Trait ImitatedWidgetTrait
 *
 * @mixin InitTrait
 * @mixin ImitatedWidgetInterface
 *
 * @property bool $imitation
 *
 * @package Common\Widgets
 * @copyright 2012-2019 Medkey
 *
 */
trait ImitatedWidgetTrait
{
    /**
     * @var bool
     */
    protected $_imitation = false;

    /**
     * @see InitTrait
     */
    public function initImitatedWidgetTrait()
    {
        if (!$this instanceof ImitatedWidgetInterface) {
            throw new \Exception('Invalid widget: must be ImitatedWidgetInterface');
        }
        if ($this->imitation) {
            $this->imitate();
        }
    }

    /**
     * @return bool
     */
    public function getImitation()
    {
        return $this->_imitation;
    }

    /**
     * @param bool $val
     * @return $this
     */
    public function setImitation($val)
    {
        $this->_imitation = (bool) $val;
        return $this;
    }

    public function imitate()
    {
    }
}
