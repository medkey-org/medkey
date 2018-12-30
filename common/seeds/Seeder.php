<?php
namespace app\common\seeds;

use app\common\db\ActiveRecord;
use app\common\helpers\CommonHelper;
use app\common\seeds\exceptions\AlreadyExistsSeedException;
use app\common\seeds\exceptions\NotWritableException;
use app\common\seeds\models\SeedFile;
use yii\base\Event;
use yii\base\InvalidParamException;
use yii\base\Component;
use yii\base\ViewContextInterface;
use yii\helpers\Inflector;

/**
 * Class Seeder
 * @package Common\Seed
 * @copyright 2012-2019 Medkey
 */
class Seeder extends Component implements ViewContextInterface
{
	/**
	 * @var string
	 */
	public $path = '@app/seeds';
	/**
	 * @var string
	 */
	public $namespace = 'app\seeds';
	/**
	 * @var callable|null
	 */
	protected $callback;


	/**
	 * @inheritdoc
	 */
	public function init()
	{
		parent::init();

		$this->path = \Yii::getAlias($this->path);

	}

	/**
	 * @return array|SeedFile[]
	 */
	public function getList($limit = null)
	{
		$result = array_diff(scandir($this->path), ['.', '..', '.gitkeep']);

		if ($limit) {
			$last = count($result) - 1;

			if ($limit > $last) {
				$limit = $last;
			}
			$result = array_slice($result, 0, $limit);
		}

		return array_map(function ($fileName) {
			return new SeedFile([
				'fileName' => $fileName,
				'path' => $this->path,
				'namespace' => $this->namespace,
			]);
		}, $result);
	}

	/**
	 * @param Seed|array|string $seed
	 * @param null|callable     $callback
	 * @throws \Exception
	 */
	public function safeApply($seed, $callback = null)
	{
		if ($callback) {
			$this->callback = $callback;
		}
		$transaction = \Yii::$app->db->beginTransaction();

		try {
			$this->apply($seed);
			$transaction->commit();
		} catch (\Exception $e) {
			$transaction->rollBack();
			throw $e;
		}
		if ($callback) {
			$this->callback = null;
		}
	}

	/**
	 * @param Seed|array|string $seed
	 * @return \app\common\logic\orm\Seed
	 */
	public function apply($seed)
	{
		$seed = $this->seed($seed);
		$result = $seed->apply();
		CommonHelper::value($this->callback, $seed);

		return $result;
	}

	/**
	 * @param string $className
	 * @return string
	 * @throws AlreadyExistsSeedException
	 * @throws NotWritableException
	 */
	public function create($className)
	{
		$className = Inflector::camelize($className);
		$fileName = $this->path . DIRECTORY_SEPARATOR . $className . '.php';

		if (file_exists($fileName)) {
			throw new AlreadyExistsSeedException("Seed with name `{$fileName}` already exists");
		}
		if (!is_writable($this->path)) {
			throw new NotWritableException("Path `{$this->path}` is not writable");
		}
		$content = \Yii::$app->view->render('resources' . DIRECTORY_SEPARATOR . 'seed', ['className' => $className, 'namespace' => $this->namespace], $this);
		file_put_contents($fileName, $content);

		return $fileName;
	}

	/**
	 * @param array       $models
	 * @param null|string $namespace
	 * @param null|string $path
	 * @return array
	 * @throws AlreadyExistsSeedException
	 * @throws NotWritableException
	 */
	public function export(array $models, $namespace = null, $path = null)
	{
		$namespace = $namespace ?: $this->namespace;
		$path = $path ? \Yii::getAlias($path) : $this->path;
		$files = [];

		foreach ($models as $modelName) {
			$baseClassName = Inflector::camelize($modelName);
			$modelClass = 'app\common\logic\orm\\' . $baseClassName;
			$fileName = $path . '/' . $baseClassName . '.php';

			if (file_exists($fileName)) {
				throw new AlreadyExistsSeedException("Seed with name `{$fileName}` already exists");
			}
			if (!is_writable($this->path)) {
				throw new NotWritableException("Path `{$path}` is not writable");
			}
			$records = call_user_func([$modelClass, 'find'])->setAccess(false)->all();
			$content = \Yii::$app->view->render('resources' . DIRECTORY_SEPARATOR . 'export', [
				'className' => $baseClassName,
				'namespace' => $namespace,
				'models' => $records,
			], $this);

			file_put_contents($fileName, $content);
			$files[] = $fileName;
		}

		return $files;
	}

    /**
     * @param $seed
     * @return array|object|string
     * @throws \yii\base\InvalidConfigException
     */
	public function seed($seed)
	{
		if (!$seed instanceof Seed) {
			if (is_string($seed)) {
				$seed = ['class' => $this->seedClass($seed)];
			}
			if (!is_array($seed)) {
				throw new InvalidParamException('Config must be class name string or config array');
			}
			$seed = \Yii::createObject($seed);
		}
		$seed->setSeeder($this);

		return $seed;
	}

	/**
	 * @param string $className
	 * @return string
	 */
	protected function seedClass($className)
	{
		if (strpos($className, '\\') !== false) {
			return $className;
		}
		$className = Inflector::camelize($className);

		return $this->namespace . '\\' . $className;
	}

	/**
	 * @return string
	 */
	public function getViewPath()
	{
		return dirname(__FILE__);
	}
}
