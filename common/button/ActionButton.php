<?php
namespace app\common\button;

use app\common\helpers\CommonHelper;
use yii\base\InvalidConfigException;
use yii\base\Widget;

/**
 * Class ActionButton
 * @package Common\Button
 * @copyright 2012-2019 Medkey
 */
class ActionButton extends Button
{
    /**
     * @var Widget
     */
    public $afterUpdateBlock;
    /**
     * @var boolean whether this button is not able. Defaults to true.
     */
    public $access = true;
    /**
     * @var bool
     */
    public $isDynamicModel = true;
    /**
     * @var string
     */
    public $queryParams;
    /**
     * @var string
     */
    public $primaryAttribute = 'id';


    /**
     * @inheritdoc
     */
    public function init()
    {
        if (isset($this->afterUpdateBlock) && !($this->afterUpdateBlock instanceof Widget)) {
            throw new InvalidConfigException('Invalid afterUpdateBlock in ActionButton.');
        }
    }

    /**
     * @return mixed
     */
    public function render()
    {
        $this->value = CommonHelper::value($this->value, $this->disabled);

        if ($this->name && is_string($this->value) && strpos('name=', $this->value) === false) {
            $this->value = preg_replace('/<(a|button)/', "<$1 name=\"{$this->name}\"", $this->value);
        }

        return $this->value;
    }
}
