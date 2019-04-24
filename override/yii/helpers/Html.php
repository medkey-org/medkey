<?php
namespace yii\helpers;

use app\common\helpers\CommonHelper;

/**
 * Class Html
 * @package Override
 * @copyright 2012-2019 Medkey
 */
class Html extends BaseHtml
{
	/**
	 * @param \yii\base\Model|DateableInterface $model
	 * @param string                            $attribute
	 * @return array|mixed|string
	 */
	public static function getAttributeValue($model, $attribute)
	{
		if ($model instanceof DateableInterface && $model->isDate($attribute)) {
			return $model->dateFormat($attribute);
		}
		return parent::getAttributeValue($model, $attribute);
	}

	/**
	 * @inheritdoc
	 */
	public static function dropDownList($name, $selection = null, $items = [], $options = [])
	{
		static::normalizeEmptyOption($options, $items);

		return parent::dropDownList($name, $selection, $items, $options);
	}

	/**
	 * @inheritdoc
	 */
	public static function listBox($name, $selection = null, $items = [], $options = [])
	{
		static::normalizeEmptyOption($options, $items);

		return parent::listBox($name, $selection, $items, $options);
	}

	/**
	 * @param array $options
	 * @param array $items
	 */
	protected static function normalizeEmptyOption(array &$options, array &$items)
	{
		$empty = ArrayHelper::remove($options, 'empty', true);

		if ($empty === true) {
			$empty = \Yii::t('app', 'Select value...');
		}
		if ($empty) {
			$item = ['' => $empty];
			$items = $item + $items;
		}
	}

	/**
	 * Добавлена возможность передать значение атрибута в виде колбека
	 *
	 * @param array $attributes
	 * @return string
	 */
	public static function renderTagAttributes($attributes)
	{
		return parent::renderTagAttributes(array_map(function ($attrValue) use ($attributes) {
			return CommonHelper::value($attrValue, $attributes, isset($attributes['value']) ? $attributes['value'] : null);
		}, $attributes));
	}
}
