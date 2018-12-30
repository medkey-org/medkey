<?php
namespace app\common\helpers;

/**
 * Class LaravelArr
 * Copypasted Laravel class, look at see block for source
 * @package Common\Helpers
 * @see https://github.com/laravel/framework/blob/5.4/src/Illuminate/Support/Arr.php
 * @copyright 2012-2019 Medkey
 */
trait LaravelArr
{
	/**
	 * Get a subset of the items from the given array.
	 *
	 * @param  array  $array
	 * @param  array|string  $keys
	 * @return array
	 */
	public static function only($array, $keys)
	{
		return array_intersect_key($array, array_flip((array) $keys));
	}

	/**
	 * Get all of the given array except for a specified array of items.
	 *
	 * @param  array  $array
	 * @param  array|string  $keys
	 * @return array
	 */
	public static function except($array, $keys)
	{
		static::forget($array, $keys);
		return $array;
	}

	/**
	 * Remove one or many array items from a given array using "dot" notation.
	 *
	 * @param  array  $array
	 * @param  array|string  $keys
	 * @return void
	 */
	public static function forget(&$array, $keys)
	{
		$original = &$array;
		$keys = (array) $keys;
		if (count($keys) === 0) {
			return;
		}
		foreach ($keys as $key) {
			// if the exact key exists in the top-level, remove it
			if (static::exists($array, $key)) {
				unset($array[$key]);
				continue;
			}
			$parts = explode('.', $key);
			// clean up before each pass
			$array = &$original;
			while (count($parts) > 1) {
				$part = array_shift($parts);
				if (isset($array[$part]) && is_array($array[$part])) {
					$array = &$array[$part];
				} else {
					continue 2;
				}
			}
			unset($array[array_shift($parts)]);
		}
	}

	/**
	 * Determine if the given key exists in the provided array.
	 *
	 * @param  \ArrayAccess|array  $array
	 * @param  string|int  $key
	 * @return bool
	 */
	public static function exists($array, $key)
	{
		if ($array instanceof \ArrayAccess) {
			return $array->offsetExists($key);
		}
		return array_key_exists($key, $array);
	}
}
