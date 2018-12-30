<?php
namespace app\common\widgets;

use app\common\db\ActiveRecord;
use app\common\helpers\CommonHelper;
use app\common\helpers\Html;
use yii\base\Model;
use yii\db\ActiveRecordInterface;
use yii\widgets\MaskedInput;
use kartik\select2\Select2;

/**
 * Class ActiveField
 * @package Common\Widgets
 * @copyright 2012-2019 Medkey
 */
class ActiveField extends \yii\bootstrap\ActiveField
{
	/**
	 * @var bool
	 */
	public $withoutWrap = false;


	/**
	 * @return string
	 */
	public function begin()
	{
		if ($this->withoutWrap) {
			return '';
		}

		return parent::begin();
	}

	/**
	 * @return string
	 */
	public function end()
	{
		if ($this->withoutWrap) {
			return '';
		}

		return parent::end();
	}

	/**
     * @param array $items
     * @param array $options
	 *
     * @return $this
     */
    public function dropDownList($items, $options = [])
    {
        $options = array_merge($this->inputOptions, $options);
        $this->adjustLabelFor($options);
        $this->parts['{input}'] = Html::activeDropDownList($this->model, $this->attribute, $items, $options);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function hiddenInput($options = [])
    {
        $options = array_merge($this->inputOptions, $options);
        $this->adjustLabelFor($options);
        $this->parts['{input}'] = Html::activeHiddenInput($this->model, $this->attribute, $options);

        return $this->label(false);
    }

	/**
	 * @param       $content
	 * @param array $options
	 * @return $this
	 */
    public function submitButton($content, array $options = [])
    {
	    $this->withoutWrap = true;
	    $this->template = "{input}";
	    $options = array_merge([
	    	'value' => $this->model->{$this->attribute},
		    'name' => Html::getInputName($this->model, $this->attribute),
	    ], $options);
	    $this->parts['{input}'] = Html::submitButton($content, $options);

	    return $this;
    }

	/**
	 * @param array $inputOptions
	 * @return $this
	 * @throws \Exception
	 */
    public function attributeInput(array $inputOptions = [])
    {
	    if (!$this->model instanceof Model) {
		    throw new \Exception(\Yii::t('app', 'Invalid model: must be Model only'));
	    }
	    $this->parts['{input}'] = Html::attributeInput($this->model, $this->attribute, array_merge($this->inputOptions, $inputOptions));

	    return $this;
    }

    /**
     * @param array $inputOptions
     * @return $this
     */
    public function emailInput(array $inputOptions = [])
    {
        $inputOptions['type'] = 'email';
        $this->parts['{input}'] = Html::activeTextInput($this->model, $this->attribute, array_merge($this->inputOptions, $inputOptions));

        return $this;
    }

    /**
     * @param array $inputOptions
     * @return ActiveField
     */
    public function phoneInput(array $inputOptions = [])
    {
        $this->parts['{input}'] = Html::activeTextInput($this->model, $this->attribute, array_merge($this->inputOptions, $inputOptions));

        return $this->inputMask(['mask' => CommonHelper::PHONE_MASK]);
    }

    /**
     * @param array $inputOptions
     * @return ActiveField
     */
    public function moneyInput(array $inputOptions = [])
    {
        $this->parts['{input}'] = Html::activeMoneyInput($this->model, $this->attribute, array_merge($this->inputOptions, $inputOptions));
        return $this;
    }

    /**
     * @param array $config
     * @return $this
     */
    public function inputMask($config)
    {
        return $this->widget(MaskedInput::className(), $config);
    }

	/**
	 * @param array $options
	 * @return $this
	 */
    public function dateInput(array $options = [])
    {
	    $this->parts['{input}'] = Html::activeDateInput($this->model, $this->attribute, $options);

	    return $this;
    }

	/**
	 * @param array $options
	 * @return $this
	 */
    public function dateTimeInput(array $options = [])
    {
	    $this->parts['{input}'] = Html::activeDateTimeInput($this->model, $this->attribute, $options);

	    return $this;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function timeInput(array $options = [])
    {
        $this->parts['{input}'] = Html::activeTimeInput($this->model, $this->attribute, $options);

        return $this;
    }

    /**
     * @todo дергать Html::select2()
     * @param array $items
     * @param array $options
     * @param array $pluginOptions
     * @param array $config
     * @return $this
     */
    public function select2($items, array $options = [], array $pluginOptions = [], $config = [])
    {
        if (!isset($pluginOptions['placeholder'])) {
            $pluginOptions['placeholder'] = \Yii::t('app', 'Select value...');
        }
        if (!isset($pluginOptions['allowClear'])) {
            $pluginOptions['allowClear'] = true;
        }
        if (!isset($options['empty'])) {
            $options['empty'] = false;
            $options['title'] = '';
        }
        $config = array_merge([
            'data' => $items,
            'language' => 'ru',
            'options' => $options,
            'pluginOptions' => $pluginOptions,
        ], $config);
        return $this->widget(Select2::class, $config);
    }

    /**
     * @inheritdoc
     */
    public function label($label = null, $options = [])
    {
        $attribute = Html::getAttributeName($this->attribute);
        if ($this->model->isAttributeRequired($attribute)) {
            Html::addCssClass($options, $this->form->requiredCssClass);
        }
        if (is_bool($label)) {
            $this->enableLabel = $label;
            if ($label === false && $this->form->layout === 'horizontal') {
                Html::addCssClass($this->wrapperOptions, $this->horizontalCssClasses['offset']);
            }
        } else {
            $this->enableLabel = true;
            $this->renderLabelParts($label, $options);
            parent::label($label, $options);
        }
        return $this;
    }
}
