<?php
namespace app\common\seeds;

/**
 * Class SimpleSequence
 * @package Common\Seed
 * @copyright 2012-2019 Medkey
 */
class SimpleSequence
{
	/**
	 * @var array
	 */
	private $types = [];
	/**
	 * @var SimpleSequence
	 */
	private static $_instance;

	/**
	 * @return SimpleSequence
	 */
	public static function instance()
	{
		if (!self::$_instance) {
			self::$_instance = new SimpleSequence();
		}
		return self::$_instance;
	}

	/**
	 * SimpleSequence constructor.
	 *
	 * Singleton
	 */
	private function __construct()
	{}

	/**
	 * @return int
	 */
	public function next($key)
	{
		if (!array_key_exists($key, $this->types)) {
			$this->types[$key] = 0;
		}

		return ++$this->types[$key];
	}
}
