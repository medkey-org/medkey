<?php
namespace app\common\seeds;

use app\common\db\ActiveRecord;
use app\common\helpers\ArrayHelper;
use app\common\helpers\Json;
use yii\base\BaseObject;
use app\common\logic\orm\Seed as SeedModel;

/**
 * Class Seed
 *
 * @property Seeder $seeder
 * @property string $description
 *
 * @property-read SimpleSequence $simpleSequence
 * @property-read ActiveRecord[] $models
 *
 * @package Common\Seed
 * @copyright 2012-2019 Medkey
 */
abstract class Seed extends BaseObject
{
	/**
	 * @var string|null
	 */
	public $model;
	/**
	 * @var array|null
	 */
	public $data;
	/**
	 * @var bool
	 */
	public $validate = false;
	/**
	 * @var bool
	 */
	public $useModelLoad = false;
	/**
	 * @var bool
	 */
	public $useSimpleIdSequence = false;
	/**
	 * @var string
	 */
	protected $_description = '';
	/**
	 * @var Seeder
	 */
	private $_seeder;
	/**
	 * @var SeedModel
	 */
	private $_seedModel;

	/**
	 * @return SeedModel
	 */
	public function apply()
	{
		$this->_seedModel = new SeedModel(['class_name' => static::className()]);
		$this->_seedModel->save();

		$this->run();

		if (!is_null($this->model) && !is_null($this->data)) {
			$this->load();
		}

		return $this->_seedModel;
	}

	/**
	 * @return void
	 */
	public function run()
	{}

	/**
	 * @return void
	 * @throws \Exception
	 */
	protected function load()
	{
		if (is_null($this->model) || is_null($this->data)) {
			throw new \Exception('Invalid data for load: `model` and `data` properties is must be set');
		}
		foreach ($this->data as $attrs) {
			if (!is_array($attrs)) {
				throw new \Exception('Invalid attributes: must be array');
			}
			$this->createModel($this->model, $attrs);
		}

		$this->model = null;
		$this->data = null;
	}

	/**
	 * @param string $modelClass
	 * @param array  $attrs
	 * @return ActiveRecord
	 * @throws \Exception
	 */
	protected function createModel($modelClass, array $attrs)
	{
		/** @var ActiveRecord $modelClass */
		$pka = $modelClass::modelIdentity();

		if ($this->useSimpleIdSequence) {
			$attrs['id'] = $this->simpleSequence->next($modelClass);
		}
		$pk = ArrayHelper::only($attrs, $pka);
		$model = null;
		/**
		 * @var ActiveRecord $model
		 */
		if (count($pka) === count($pk)) {
			$models = $modelClass::find()->setAccess(false)->where($pk)->notDeleted()->all();

			/**
			 * remove doubles
			 * @see ActiveRecord::modelIdentity()
			 */
			while (count($models) > 1) {
				array_shift($models)->delete();
			}
			$model = reset($models);
		}
		if (!$model && isset($attrs['id'])) {
            $model = $modelClass::find()->where(['id' => $attrs['id']])->setAccess(false)->one();
		}
		if (!$model) {
			$model = new $modelClass;
		}
		if ($this->useModelLoad) {
			$model->load($attrs, '');
		} else {
			$this->unguardFill($model, $attrs);
		}
		if ($this->validate && !$model->validate()) {
			throw new \Exception('Invalid data: ' . implode(', ', $model->errors));
		}
		$model->restore(false);
		if (!$model->save()) {
            $errors = Json::encode($model->getErrors());
            \Yii::warning('Cannot remove record, reason: ' . $errors);
        }

		$this->addSeedRecord($model);

		return $model;
	}

	/**
	 * @param ActiveRecord $model
	 * @param array        $attrs
	 */
	private function unguardFill($model, array $attrs)
	{
		\Yii::configure($model, $attrs);
	}

	/**
	 * @param string|array $config
	 * @return SeedModel
	 */
	public function call($config)
	{
		return $this->getSeeder()->apply($config);
	}

	/**
	 * @param ActiveRecord $model
	 * @return \app\common\logic\orm\SeedRecord
	 */
	protected function addSeedRecord(ActiveRecord $model)
	{
		return $this->_seedModel->addActiveRecord($model);
	}

	/**
	 * @return int
	 */
	protected function getDefaultUnionId()
	{
		return \Yii::$app->integration->getSelf()->unionId;
	}

	/**
	 * @return SimpleSequence
	 */
	public function getSimpleSequence()
	{
		return SimpleSequence::instance();
	}

	/**
	 * @return Seeder
	 */
	public function getSeeder()
	{
		return $this->_seeder;
	}

	/**
	 * @param Seeder $seeder
	 */
	public function setSeeder(Seeder $seeder)
	{
		$this->_seeder = $seeder;
	}

	/**
	 * @return string
	 */
	public function getDescription()
	{
		return $this->_description;
	}

    /**
     * @return ActiveRecord[]
     */
	public function getModels()
    {
        return $this->_seedModel->models;
    }
}
