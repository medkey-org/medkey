<?php
namespace app\common\helpers;

/**
 * Class CommonHelper
 * @package Common\Helpers
 * @copyright 2012-2019 Medkey
 */
class CommonHelper
{
    const FORMAT_TIME_UI = 'HH:mm';
    const FORMAT_TIME_DB = 'HH:mm';
    const FORMAT_DATE_UI = 'dd.MM.yyyy';
    const FORMAT_DATEPICKER_UI = 'dd.mm.yyyy';
    const FORMAT_DATETIMEPICKER_UI = 'dd.mm.yyyy hh:ii';
    const FORMAT_DATETIME_UI = 'dd.MM.yyyy HH:mm';
    const FORMAT_DATE_DB = 'yyyy-MM-dd';
    const FORMAT_DATETIME_DB = 'yyyy-MM-dd HH:mm';
    const PHONE_MASK = '+7 (999) 999-99-99';
    const CURRENCY_SUM_MASK = '[9{1,9}][.99]';


	/**
	 * @param mixed|callable $value
	 * @param mixed          $_ optional arguments
	 * @return mixed
	 */
	public static function value($value, $_ = null)
	{
		if (!is_string($value) && is_callable($value)) {
			$args = func_num_args() > 1 ? array_slice(func_get_args(), 1) : [];
			$value = static::valueArray($value, $args);
		}

		return $value;
	}

	/**
	 * @param mixed|callable $value
	 * @param array          $args
	 * @return mixed
	 */
	public static function valueArray($value, $args = [])
	{
		if (!is_string($value) && is_callable($value)) {
			$value = call_user_func_array($value, $args);
		}

		return $value;
	}

	/**
	 * @param array|callable $options
	 * @return array
	 */
	public static function optionsValue($options)
	{
		$options = static::value($options);

		if (is_array($options)) {
			foreach ($options as $key => $value) {
				$options[$key] = static::optionsValue($value);
			}
		}

		return $options;
	}
}
