<?php
namespace app\common\logic\orm;

use app\common\db\ActiveRecord;
use app\common\db\ActiveRecordHistoryInterface;
use app\common\db\ActiveRecordHistoryTrait;
use app\common\db\Query;

/**
 * Class SeedRecord
 *
 * @property int    $seed_id
 * @property string $model
 * @property string $pk
 *
 * @property array $condition
 *
 * @property-read Seed         $seed
 * @property-read ActiveRecord $modelRecord
 *
 * @package Common\Logic
 * @copyright 2012-2019 Medkey
 */
class SeedRecord extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->history = false;
        parent::init();
    }

	/**
	 * @return array
	 */
	public function rules()
	{
		return [
			[ ['model', 'pk'], 'required', 'on' => 'default' ],
			[ ['model', 'pk'], 'string', 'on' => 'default' ],
			[ ['condition'], 'safe', 'on' => 'default' ],
		];
	}

	/**
	 * @param array $pk
	 */
	public function setCondition(array $pk)
	{
		$this->pk = serialize($pk);
	}

	/**
	 * @return array
	 */
	public function getCondition()
	{
		return unserialize($this->pk);
	}

	/**
	 * @return Query
	 */
	public function getModelRecord()
	{
		/**
		 * @var Query $query
		 */
		$className = $this->model;
		$query = $className::find()->setAccess(false);

		return $query->where($this->condition);
	}

	/**
	 * @return \app\common\db\ActiveQuery
	 */
	public function getSeed()
	{
		return $this->hasOne(Seed::className(), ['id' => 'seed_id'])->setAccess(false);
	}
}
