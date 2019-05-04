<?php
namespace app\common\card;

use app\common\db\ActiveRecord;
use app\common\helpers\CommonHelper;
use app\common\widgets\ActiveForm;
use yii\base\Model;
use yii\base\BaseObject;
use app\common\helpers\Html;
use yii\helpers\ArrayHelper;

/**
 * Class CardDataAttribute
 * @package Common\Card
 * @copyright 2012-2019 Medkey
 */
abstract class CardDataAttribute extends BaseObject
{
    const LABEL_COL_SIZE = '4';
    const VALUE_COL_SIZE = '8';

    /**
     * @var Model
     */
    public $model;
    /**
     * @var
     */
    public $attribute;
    /**
     * @var \Closure|string
     */
    public $value;
    /**
     * @var \Closure|string
     */
    public $label;
    /**
     * @var string
     */
    public $type;
    /**
     * @var int $count
     */
    public $count;
	/**
	 * @var ActiveForm
	 */
    public $form;
    /**
     * @var int
     */
    public $colSize;
    /**
     * @var int
     */
    public $labelSize;
    /**
     * @var int
     */
    public $valueSize;
    /**
	 * @var array
	 */
	public $inputOptions = [];
	/**
	 * @var bool
	 */
	public $multiline = false;


    public function render()
    {
	    $label = $this->renderLabel();
	    $value = $this->renderValue();
	    if (!isset($label) && !isset($value)) {
		    return null;
	    }
        echo Html::beginTag('div', ['class' => 'row form-group']);
	        echo Html::beginTag('div', ['class' => 'text-right col-md-' . (isset($this->labelSize) && $this->checkSizes() ? $this->labelSize : $this::LABEL_COL_SIZE)]);
			    if ($label) {
			        $labelOptions = [];
			        if ($this->model->scenario !== Model::SCENARIO_DEFAULT && $this->model->isAttributeRequired($this->attribute)) {
			            Html::addCssClass($labelOptions, ActiveForm::REQUIRED_CLASS);
                    }
                    Html::addCssClass($labelOptions, 'text-muted control-label attribute-label');
				    echo Html::tag('label', $label, $labelOptions);
			    }
	        echo Html::endTag('div');
	        echo Html::beginTag('div', ['class' => 'col-md-' . (isset($this->labelSize) && $this->checkSizes() ? $this->valueSize : $this::VALUE_COL_SIZE)]);
	            if (is_array($value)) {
                    echo Html::ul($value);
                } else {
                    echo Html::tag('div', $value, [
                        'class' => 'attribute-value' . ($this->multiline ? '' : ' attribute-value-line'),
                        'title' => $this->multiline ? false : strip_tags($value),
                    ]);
                }
	        echo Html::endTag('div');
        echo Html::endTag('div');
    }

    /**
     * @return bool
     */
    protected function checkSizes()
    {
        $check = false;
        $sum = $this->labelSize + $this->valueSize;
        if ($sum > 0 && $sum <= 12) {
            $check = true;
        }
        return $check;
    }

    /**
     * @return integer
     */
    protected function getLabelColSize()
    {
        return 12 - $this->getValueColSize();
    }

    /**
     * @return integer
     */
    protected function getValueColSize()
    {
        switch ($this->count) {
            case 1:
                return 10;
            case 2:
                return 8;
            default:
                return 6;
        }
    }

    public function renderLabel()
    {
        if ($this->label === false) {
            return null;
        }
        if ($this->label instanceof \Closure) {
            return call_user_func($this->label, $this->model);
        }
        if (isset($this->label) && strlen($this->label) > 0) {
            return $this->label;
        }
        if ($this->model instanceof Model) {
            return $this->model->getAttributeLabel($this->attribute);
        }
        return false;
    }

	/**
	 * @return string
	 */
    public function renderValue()
    {
        if ($this->value === false) {
            return null;
        }
	    $value = CommonHelper::value($this->value, $this->model, $this->form);
	    if ($value !== null) {
		    return $value;
	    }
	    if (!empty($this->attribute)) {
            $value = ArrayHelper::getValue($this->model, $this->attribute);
        }
	    if ($this->model instanceof ActiveRecord && $this->model->isDate($this->attribute)) {
		    $value = $this->model->dateFormat($this->attribute);
	    }
	    return Html::encode($value);
    }
}
