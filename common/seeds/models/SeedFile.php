<?php
namespace app\common\seeds\models;

use app\common\base\Model;
use yii\helpers\Inflector;

/**
 * Class SeedFile
 *
 * @property-read string $name
 * @property-read string $className
 * @property-read string $baseClassName
 *
 * @package Common\Seed
 * @copyright 2012-2019 Medkey
 */
class SeedFile extends Model
{
	/**
	 * @var string
	 */
	public $fileName;
	/**
	 * @var string
	 */
	public $path;
	/**
	 * @var string
	 */
	public $namespace;


	/**
	 * @return string
	 */
	public function getName()
	{
		return Inflector::camel2id($this->baseClassName, '_');
	}

	/**
	 * @return string
	 */
	public function getClassName()
	{
		return $this->namespace . '\\' . $this->baseClassName;
	}

	/**
	 * @return string
	 */
	public function getBaseClassName()
	{
		return preg_replace('/\.php$/', '', $this->fileName);
	}

	/**
	 * @return array
	 */
	public function rules()
	{
		return [
			[ ['fileName', 'path'], 'string', 'on' => 'default' ],
		];
	}
}
