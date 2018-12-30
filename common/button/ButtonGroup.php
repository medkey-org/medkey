<?php
namespace app\common\button;

use yii\base\BaseObject;
use app\common\helpers\Html;

/**
 * Button template renderer
 * @package Common\Button
 * @copyright 2012-2019 Medkey
 */
class ButtonGroup extends BaseObject
{
    /**
     * @var array Button configuration
     */
    public $buttons;
    /**
     * @var string Button template
     */
    public $buttonTemplate;
    /**
     * @var string Default button class
     */
    public $defaultButtonClass = 'app\common\button\Button';
    /**
     * @var array Button configuration
     */
    public $buttonConfig = [];
    /**
     * @var bool Are buttons need to be groped?
     */
    public $group = false;
    /**
     * @var array Custom options if buttons are grouped ($group == true)
     */
    public $groupOptions = [];
    /**
     * @var array Internal button collection
     */
    private $_buttons;


    /**
     * Fill internal buttons collection
     */
    public function init()
    {
        foreach ($this->buttons as $key => $value) {
	        if (is_array($value) && !is_numeric($key)) {
		        $value = array_merge(['name' => $key], $value);
	        }
            $this->_buttons[$key] = $this->buildButton($value);
        }

        parent::init();
    }

    /**
     * Build button instance
     * @param $value
     * @return null|object
     * @throws \yii\base\InvalidConfigException
     */
    protected function buildButton($value)
    {
        if (!is_array($value)) {
            return null;
        }

        $config = array_merge($value, $this->buttonConfig);

        if (!isset($config['class'])) {
            $config['class'] = $this->defaultButtonClass;
        }

        return \Yii::createObject($config);
    }

    /**
     * Render buttons of template
     * @return string
     */
    public function render()
    {
        $output = '';
        $output .= preg_replace_callback('/\\{([АБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЬЫЪЭЮЯабвгдеёжзийклмнопрстуфхцчшщьыъэюя \w\-\/]+)\\}/', function ($matches) {
            $output = '';
            $buttonTemplate = str_replace('{', '', $matches[0]);
            $buttonTemplate = str_replace('}', '', $buttonTemplate);
            if (isset($this->buttons[$buttonTemplate]) && isset($this->_buttons[$buttonTemplate]) && $this->_buttons[$buttonTemplate] instanceof ButtonInterface) {
                $output .= $this->_buttons[$buttonTemplate]->render();
                $output .= ' ';
                return $output;
            }
        }, $this->buttonTemplate);
        if ($this->group === true) {
            $output = Html::beginTag('div', array_merge([
                'class' => 'btn-group',
                'role' => 'group',
                'aria-label' => '',
            ], $this->groupOptions)) . $output . Html::endTag('div');
        }

        return $output;
    }
}
