<?php
namespace app\common\helpers;

use yii\base\Arrayable;
use yii\base\InvalidParamException;

/**
 * Class ArrayHelper
 * @package Common\Helpers
 * @copyright 2012-2019 Medkey
 */
class ArrayHelper extends \yii\helpers\ArrayHelper
{
	use LaravelArr;

	/**
	 * @param array|array[]|\ArrayAccess[] $array
	 * @param array|callable               $condition
	 * @param bool                         $strict
	 * @return array
	 */
	public static function filterBy($array, $condition, $strict = false)
	{
		if (!is_callable($condition) && !is_array($condition)) {
			throw new InvalidParamException('Condition must be callable or array');
		}
		$callback = is_callable($condition) ? $condition : function ($item) use ($condition, $strict) {
			if (!$item instanceof \ArrayAccess && !is_array($item)) {
				return false;
			}
			$result = true;

			foreach ($condition as $attr => $value) {
				if (!static::exists($item, $attr)) {
					return false;
				}
				$result = $result && ($strict ? ($item[$attr] === $value) : ($item[$attr] == $value));
			}
			return $result;
		};
		return array_filter($array, $callback);
	}

	/**
	 * @param array|array[]|\ArrayAccess[] $array
	 * @param array|callable               $condition
	 * @param bool                         $strict
	 * @return mixed
	 */
	public static function findBy($array, $condition, $strict = false)
	{
	    $data = static::filterBy($array, $condition, $strict);

	    if (!$data) {
	        return null;
        }

		return reset($data);
	}

    /**
     * @param array $array
     *
     * @return array
     */
    public static function intToStringRecursive($array)
    {
        $stringArray = [];

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $stringArray[$key] = static::intToStringRecursive($value);
            } elseif (is_scalar($value) && $value !== null && !is_bool($value)) {
                $stringArray[$key] = static::toStringValue($value);
            } elseif (is_object($value) && $value instanceof Arrayable) {
                $stringArray[$key] = $value->toArray();
            }
        }

        if (!ArrayHelper::isAssociative($array)) {
	        return $stringArray;
        }

        return array_merge($array, $stringArray);
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    public static function toStringValue($value)
    {
        return strval($value);
    }

    /**
     * @param array $arrayFrom
     * @param array $arrayTo
     */
    public static function getRecursiveDiff($arrayFrom, $arrayTo)
    {
    }

    /**
     * @param array $arrayFrom
     * @param array $arrayTo
     *
     * @return array
     */
    public static function getDiff($arrayFrom, $arrayTo)
    {
        return array_diff_assoc($arrayFrom, $arrayTo);
    }
}
