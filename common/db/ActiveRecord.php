<?php
namespace app\common\db;

use app\common\base\PrepareModelInterface;
use app\common\dto\Dto;
use app\common\helpers\ClassHelper;
use app\common\helpers\CommonHelper;
use yii\base\InvalidArgumentException;
use yii\base\InvalidValueException;
use yii\base\Model;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQueryInterface;
use yii\db\Expression;
use app\modules\security\models\orm\User;
use app\common\helpers\ArrayHelper;
use yii\helpers\Inflector;

/**
 * Class ActiveRecord
 * @property string $id
 * @property string $history_start_date
 * @property int $history_version
 * @property string $user_created_id
 * @property string $user_updated_id
 * @property int $created_at timestamp with time zone
 * @property int $updated_at timestamp with time zone
 * @property int $deleted_at timestamp with time zone
 * @property int $is_deleted 0 or 1 (not or deleted record)
 * @property array[] $changedAttributes
 * @package common\db\db
 * @copyright 2012-2019 Medkey
 */
class ActiveRecord extends \yii\db\ActiveRecord implements PrepareModelInterface, ActiveRecordHistoryInterface
{
    use ActiveRecordHistoryTrait;

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_DELETE = 'delete';
    const SCENARIO_SEARCH = 'search';
    const IS_DELETE_TRUE = 1;
    const IS_DELETE_FALSE = 0;

    public $allScenarios = [
        self::SCENARIO_DEFAULT,
        self::SCENARIO_CREATE,
        self::SCENARIO_UPDATE,
        self::SCENARIO_DELETE,
        self::SCENARIO_SEARCH,
    ];
    public static $systemFields = [
        'user_created_id',
        'user_updated_id',
        'history_version',
        'history_start_date',
        'created_at',
        'updated_at',
        'deleted_at',
        'is_deleted'
    ];
    /**
     * Список реляций (классов), у которых будут выводиться удаленные связи.
     * @var array
     */
    public $deletedAvailableRelations = [];
	/**
	 * @var bool
	 */
	public static $listAllAsArray = true;
    /**
     * @var array
     */
    private $_dateTypeMap = [];
	/**
	 * @var array
	 */
	private static $_listAllCache = [];

    /**
     * @todo для других сценариев переопределять в дочерних классах
     * @inheritdoc
     */
    public function transactions()
    {
        return [
            'default' => self::OP_INSERT | self::OP_UPDATE | self::OP_DELETE,
            'create' => self::OP_INSERT | self::OP_UPDATE | self::OP_DELETE,
            'update' => self::OP_INSERT | self::OP_UPDATE | self::OP_DELETE,
//            'delete' => self::OP_INSERT | self::OP_UPDATE | self::OP_DELETE
        ];
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->_dateTypeMap = [
            'datetime' => CommonHelper::FORMAT_DATETIME_UI,
            'timestamp' => CommonHelper::FORMAT_DATETIME_UI,
            'time' => CommonHelper::FORMAT_TIME_UI,
            'date' => CommonHelper::FORMAT_DATE_UI
        ];
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function optimisticLock()
    {
        return $this->hasProperty('history_version') ? 'history_version' : null;
    }

    /**
     * @todo возможно нет поля title для карточки
	 * @return null|string
	 */
	public function modelTitle()
	{
		$labels = static::attributeLabels();
		return array_key_exists('modelTitle', $labels) ? $labels['modelTitle'] : null;
	}

    /**
     * @return string[]
     */
    public static function modelIdentity()
    {
        return static::primaryKey();
    }

    /**
     * @inheritdoc
     */
    public static function primaryKey()
    {
        return ['id'];
    }

	/**
	 * @return string
	 */
    public static function entityName()
    {
        return ClassHelper::getShortName(static::class);
    }

	/**
	 * @param string $attr
	 * @return bool
	 */
    public static function attributeExists($attr)
    {
	    return (new static())->hasAttribute($attr);
    }

	/**
	 * @param bool $asArray
	 * @return mixed
	 */
	public function getModelIdentity($asArray = false)
	{
		$keys = self::modelIdentity();
		if (!$asArray && count($keys) === 1) {
			return isset($this->{$keys[0]}) ? $this->{$keys[0]} : null;
		} else {
			$values = [];
			foreach ($keys as $name) {
				$values[$name] = isset($this->{$name}) ? $this->{$name} : null;
			}

			return $values;
		}
	}

    /**
     * @return string
     */
    public static function titleAttribute()
    {
        return 'title';
    }

	/**
	 * Список констант модели со значениями
	 *
	 * @return array
	 */
	public static function constants()
	{
		$ref = new \ReflectionClass(static::className());
		return $ref->getConstants();
	}

	/**
	 * @return array
	 */
	public function scenarioLabels()
	{
		return [
			'default' => \Yii::t('app','Show'),
			'create' => \Yii::t('app', 'Create'),
			'update' => \Yii::t('app', 'Update'),
			'delete' => \Yii::t('app', 'Delete'),
			'search' => \Yii::t('app', 'Search'),
		];
	}

	/**
	 * @param string $scenario
	 * @return string
	 */
	public function getScenarioLabel($scenario)
	{
		$labels = $this->scenarioLabels();

		if (!array_key_exists($scenario, $labels)) {
			return Inflector::humanize($scenario);
		}
		return $labels[$scenario];
	}

    /**
     * @param string $attribute
     * @return null|string
     */
	public function getTypeColumn($attribute)
    {
        $colType = self::getTableSchema()->getColumn($attribute);
        if ($colType) {
            $colType = $colType->type;
            return $colType;
        }

        return null;
    }

    /**
     * @param string $attribute
     * @return bool
     */
	public function isDate($attribute)
    {
        $colType = $this->getTypeColumn($attribute);
        if ($colType && array_key_exists($colType, $this->_dateTypeMap)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $attribute
     * @return null|string
     */
    public function dateFormat($attribute)
    {
        $colType = $this->getTypeColumn($attribute);
        if (!$this->isDate($attribute)) {
            return null;
        }
        switch ($colType) {
            case 'datetime':
            case 'timestamp':
                return \Yii::$app->formatter->asDatetime($this->{$attribute}, CommonHelper::FORMAT_DATETIME_UI);
            case 'date':
                return \Yii::$app->formatter->asDate($this->{$attribute}, CommonHelper::FORMAT_DATE_UI);
            case 'time':
                return \Yii::$app->formatter->asTime($this->{$attribute}, CommonHelper::FORMAT_TIME_UI);
        }
    }

	/**
	 * @param bool $asArray
	 * @param string $valueField
	 * @param string $keyField
	 * @param array|callable $callback callback or condition
	 * @return array
	 */
	public static function listAll($asArray = null, $valueField = null, $keyField = null, $callback = null)
	{
		$cache = self::getListAllCache();

		if (is_null($asArray) && is_null($valueField) && is_null($keyField) && is_null($callback) && !is_null($cache)) {
			return $cache;
		}
		if ($valueField === null) {
			$valueField = static::titleAttribute();
		}
		if ($keyField === null) {
			$keyField = 'id';
		}
		if ($asArray === null) {
			$asArray = static::$listAllAsArray;
		}
		$query = static::find()->notDeleted();

		if ($asArray) {
			$query->select([$keyField, $valueField])->asArray()
                  ->orderBy($valueField);
		}
		if ($callback) {
			$condition = CommonHelper::value($callback, $query, $valueField, $keyField);

			if ($condition) {
				$query->andWhere($condition);
			}
		}
		$list = ArrayHelper::map($query->all(), $keyField, $valueField);

		if (is_null($asArray) && is_null($valueField) && is_null($keyField) && is_null($callback)) {
			self::setListAllCache($list);
		}

		return $list;
	}

	/**
	 * @param bool   $asArray
	 * @param string $valueField
	 * @param string $keyField
	 * @param array  $condition
	 * @return array
     * @deprecated
	 */
	public static function listAllBy($asArray = null, $valueField = null, $keyField = null, array $condition = [])
	{
		return static::listAll($asArray, $valueField, $keyField, function (ActiveQuery $query) use ($condition) {
			$query->andWhere($condition);
		});
	}

	/**
	 * @param array $list
	 */
	private static function setListAllCache(array $list)
	{
		self::$_listAllCache[static::className()] = $list;
	}

	/**
	 * @return array
	 */
	private static function getListAllCache()
	{
		$className = static::className();

		if (!array_key_exists($className, self::$_listAllCache)) {
			return null;
		}

		return self::$_listAllCache[$className];
	}

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()')
            ],
            [
                'class' => AuthorAttributeBehavior::className(),
            ],
        ];
    }

    /**
     * @param int|array $condition
     * @throws Exception
     * @return static ActiveRecord
     */
    public static function findOneEx($condition)
    {
        /** @var ActiveRecord $record */
        $record = static::findOne($condition);
        if ($record === null) {
            throw new Exception('Запись не найдена в базе данных'); // todo normalize text
        }

        return $record;
    }

    /**
     * @inheritdoc
     */
    public static function findOne($condition)
    {
        $key = call_user_func([static::class, 'tableColumns'], 'is_deleted');
        if (!ArrayHelper::isAssociative($condition)) {
            $primaryKey = static::primaryKey();
            if (isset($primaryKey[0])) {
                $condition = [
                    static::tableColumns($primaryKey[0]) => $condition,
                    $key => self::IS_DELETE_FALSE,
                ];
            } else {
                throw new InvalidArgumentException('"' . get_called_class() . '" должен содержать первичный ключ.');
            }
        } else {
            $condition[$key] = isset($condition['is_deleted']) ? $condition['is_deleted'] : self::IS_DELETE_FALSE;
        }

        return parent::findOne($condition);
    }

	/**
     * @param int|string|array|ActiveRecord $model
     * @param string $scenario
     * @param array $data
     * @throws InvalidParamException
     *
     * @return ActiveRecord
     */
    public static function ensure($model, $scenario = self::SCENARIO_DEFAULT, $data = [])
    {
        if (is_scalar($model)) {
            $m = static::findOneEx($model);
            $m->setScenario($scenario);
            $m->load($data);
            return $m;
        } elseif (is_array($model) && ArrayHelper::isAssociative($model)) {
            $primaryKey = static::primaryKey();
            $condition = [];
            $isPk = true;
            foreach ($primaryKey as $column) {
                if (!isset($model[$column])) {
                    $isPk = false;
                    $condition = [];
                    break;
                }
                $condition[$column] = $model[$column];
            }
            if (!$isPk) {
                $columns = static::getTableSchema()->columns;
                foreach ($model as $column => $value) {
                    if (isset($columns[$column])) {
                        $condition[$column] = $value;
                    }
                }
            }
            if (empty($condition)) {
                throw new InvalidValueException('Empty query condition');
            }
            if (!empty($model['scenario'])) { // todo покрасивее сделать
                $scenario = $model['scenario'];
            }
            $m = static::findOneEx($condition);
            $m->setScenario($scenario);
            $m->load($data);
            return $m;
        } elseif ($model instanceof static) {
            $m = clone $model;
            $m->setScenario($scenario);
            $m->load($data);
            return $m;
        }
        throw new InvalidValueException('Incorrect model');
    }

    /**
     * @todo abstract for model yet
     * @param Dto $dto
     * @param bool $safeOnly
     * @return bool
     * @deprecated
     */
    public function loadDto($dto, bool $safeOnly = true) : bool
    {
        if (!($dto instanceof Dto)) {
            throw new InvalidValueException('Param is not instance of Dto class');
        }
        if (empty($dto->getEntity())) {
            return true;
        }
        $attributes = array_flip($safeOnly ? $this->safeAttributes() : $this->attributes());
        foreach ($attributes as $attribute => $value) {
            if (isset($dto->{$attribute})) {
                $this->{$attribute} =  $dto->{$attribute};
            }
        }
        return true;
    }

    /**
     * @param Model $form
     * @param bool $safeOnly
     * @return bool
     */
    public function loadForm($form, bool $safeOnly = true) : bool
    {
        if (!($form instanceof Model)) {
            throw new InvalidValueException('Param is not instance of ' . Model::class . ' class');
        }
        $attributes = array_flip($safeOnly ? $this->safeAttributes() : $this->attributes());
        foreach ($attributes as $attribute => $value) {
            if (isset($form->{$attribute})) {
                $this->{$attribute} =  $form->{$attribute};
            }
        }
        return true;
    }

    /**
     * @param array|int|ActiveRecord $model
     * @param string $scenario
     * @param array $data
     *
     * @return ActiveRecord
     */
    public static function ensureWeak($model, $scenario = self::SCENARIO_DEFAULT, $data = [])
    {
        try {
            $m = static::ensure($model, $scenario, $data);
            return $m;
        } catch (\Exception $e) {
            if ($model instanceof ActiveRecord) {
                $m = clone $model;
                $m->setScenario($scenario);
                $m->load($data);
                return $m;
            } elseif (is_array($model) && ArrayHelper::isAssociative($model)) {
                $m = new static([
                    'scenario' => $scenario,
                ]);
                $m->setAttributes($model);
                return $m;
            }
            if (!empty($model['scenario'])) { // todo покрасивее сделать
                $scenario = $model['scenario'];
            }
            $m = new static([
                'scenario' => $scenario
            ]);
            $m->load($data);
            return $m;
        }
    }

    /**
     * @param string $class
     * @param array $link
     * @return ActiveQuery
     */
//    public function hasMany($class, $link)
//    {
//        $relation = parent::hasMany($class, $link);
//        if (!empty($this->deletedAvailableRelations) && is_array($this->deletedAvailableRelations) && in_array($class, $this->deletedAvailableRelations)) {
//            return $relation;
//        }
//
//        return $relation
//            ->andOnCondition(['!=', $relation->alias() . '.[[is_deleted]]', self::IS_DELETE_TRUE]);
//    }

    /**
     * @param string $class
     * @param array $link
     * @return \yii\db\ActiveQuery
     */
//    public function hasOne($class, $link)
//    {
//        $relation = parent::hasOne($class, $link);
//        if (!empty($this->deletedAvailableRelations) && is_array($this->deletedAvailableRelations) && in_array($class, $this->deletedAvailableRelations)) {
//            return $relation;
//        }
//
//        return $relation
//            ->andOnCondition(['!=', $relation->alias() . '.[[is_deleted]]', self::IS_DELETE_TRUE]);
//    }

    /**
     * @return ActiveQuery
     */
    public static function find()
    {
	    /** @var ActiveQuery $query */
        $query = \Yii::createObject(ActiveQuery::class, [get_called_class()]);
        return $query;
    }

	/**
	 * @param bool $save
	 * @return $this
	 */
    public function restore($save = true)
    {
    	if (!$this->is_deleted) {
    		return $this;
	    }
    	$this->is_deleted = self::IS_DELETE_FALSE;
    	$this->deleted_at = null;

    	if ($save) {
	        $this->save();
    	}

	    return $this;
    }

    /**
     * @return mixed
     */
    public function attributeLabelsOverride()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge([
            'id' => 'ID',
            'name' => 'Наименование',
            'description' => 'Описание',
            'created_by' => 'Исполнитель',
            'modified_by' => 'Последнее изменение',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата изменения',
            'deleted_at' => 'Дата удаления',
            'is_deleted' => 'Удалён?',
            'user_created' => 'Автор',
        ], $this->attributeLabelsOverride());
    }

	/**
	 * @param array|string $cols
     *
	 * @return array|string
	 */
    public static function tableColumns($cols)
    {
	    $isSingle = !is_array($cols);
        $table = call_user_func([static::class, 'tableName']);
	    $cols = array_map(function ($c) use ($table) {
		    if (!in_array($table, ['active_record'])) {
			    $c = $table . '.' . $c;
		    }
		    return $c;
	    }, (array)$cols);

	    return $isSingle ? reset($cols) : $cols;
    }

    /**
     * Получить модель создателя записи
     * @return \yii\db\ActiveQuery
     */
    public function getUserCreated()
    {
        return $this->hasOne(User::class, ['id' => 'user_created_id']);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = [self::SCENARIO_DEFAULT => [], self::SCENARIO_CREATE => [], self::SCENARIO_UPDATE => [], self::SCENARIO_DELETE => []];
        foreach ($this->getValidators() as $validator) {
            foreach ($validator->on as $scenario) {
                $scenarios[$scenario] = [];
            }
            foreach ($validator->except as $scenario) {
                $scenarios[$scenario] = [];
            }
        }
        $names = array_keys($scenarios);

        foreach ($this->getValidators() as $validator) {
            if (empty($validator->on) && empty($validator->except)) {
                foreach ($names as $name) {
                    foreach ($validator->attributes as $attribute) {
                        $scenarios[$name][$attribute] = true;
                    }
                }
            } elseif (empty($validator->on)) {
                foreach ($names as $name) {
                    if (!in_array($name, $validator->except, true)) {
                        foreach ($validator->attributes as $attribute) {
                            $scenarios[$name][$attribute] = true;
                        }
                    }
                }
            } else {
                foreach ($validator->on as $name) {
                    foreach ($validator->attributes as $attribute) {
                        $scenarios[$name][$attribute] = true;
                    }
                }
            }
        }

        foreach ($scenarios as $scenario => $attributes) {
            if (!empty($attributes)) {
                $scenarios[$scenario] = array_keys($attributes);
            }
        }

        return $scenarios;
    }

    /**
     * Получить модель пользователя последнего обновившего запись
     * @return \yii\db\ActiveQuery
     */
    public function getUserUpdated()
    {
        return $this->hasOne(User::class, ['id' => 'user_updated_id']);
    }

    /**
     * @param string $entity
     * @return array
     */
    public static function getFields($entity)
    {
        if (empty($entity)) {
            return [];
        }
        $schema = \Yii::$app->db->schema->getTableSchema($entity);
        $columns = array_keys($schema->columns);
        if (empty($columns)) {
            return [];
        }
        $systemFields = static::$systemFields;
        if (!is_array($systemFields)) {
            return $columns;
        }
        foreach ($systemFields as $f) {
            ArrayHelper::removeValue($columns, $f);
        }
        return array_combine($columns, $columns);
    }

    /**
     * @return array
     */
    public function fields()
    {
        $fields = array_keys($this->getAttributes());
        $systemFields = static::$systemFields;
        if (is_array($systemFields)) {
            foreach ($systemFields as $f) {
                ArrayHelper::removeValue($fields, $f);
            }
        }
        return array_combine($fields, $fields);
    }
}
