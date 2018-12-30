<?php
namespace app\common\db;

use app\common\helpers\ArrayHelper;
use app\common\helpers\CommonHelper;

/**
 * For only SQL DB
 * Class DynamicAttributeTrait
 * @todo посмотреть refresh()
 * @todo Добавить возможность просто добавлять новые динамические атрибуты ( или удалять старые ).
 * @todo или слать запрос SELECT по ID, который возвращает INSERT/UPDATE и вставлять атрибуты в saveHistory
 * @todo сейчас getDynamicAttribute возвращает не в той плоскости атрибуты (в бд они хранятся иначе)
 * @todo написать CHEKER проверяющий таблицы на коллизии в именах атрибутов
 * @todo добавить при добавлении атрибута динамического в таблицу,
 * @todo например, dyn__member проверку на то,
 * @todo что такой атрибут не должен существовать в основной таблице (например, member)
 * @todo validate dynamicAttribute для этого нужно ->attributes объединить с ->dynamicAttributes
 * @todo переопределить метод ActiveRecord::fields
 * @package Common\DB
 * @copyright 2012-2019 Medkey
 * @deprecated
 */
trait DynamicAttributeTrait
{
    /**
     * @var array
     */
    private $_dynamicAttributes = [];
    /**
     * @var array
     */
    private $_oldDynamicAttributes;


    /**
     * @return string
     */
    public static function dynamicTableName()
    {
        return '{{%dyn__' . \Yii::$app->db->schema->getRawTableName(static::tableName()) . '}}';
    }

    /**
     * @return string
     */
    public static function dynamicValueTableName()
    {
        return '{{%dyn_val__' . \Yii::$app->db->schema->getRawTableName(static::tableName()) . '}}';
    }

    /**
     * @inheritdoc
     */
    public static function populateRecord($record, $row)
    {
        parent::populateRecord($record, $row);

        static::populateDynamicAttributeRecord($record);
    }

    /**
     * @todo пока историчность не реализована
     * @return string
     */
    public static function historyDynamicValueTableName()
    {
        return '{{%his__dyn_val__' . \Yii::$app->db->schema->getRawTableName(static::tableName()) . '}}';
    }

    /**
     * @param string[]|null $names
     * @return array
     */
    public function getDirtyDynamicAttributes($names = null)
    {
        if ($names === null) {
            $names = $this->dynamicAttributes();
        }
        $names = array_flip($names);
        $attributes = [];
        if ($this->_oldDynamicAttributes === null) {
            foreach ($this->_dynamicAttributes as $name => $value) {
                if (isset($names[$name])) {
                    $attributes[$name] = $value;
                }
            }
        } else {
            foreach ($this->_dynamicAttributes as $name => $value) {
                if (isset($names[$name]) && (!array_key_exists($name, $this->_oldDynamicAttributes) || $value !== $this->_oldDynamicAttributes[$name])) {
                    $attributes[$name] = $value;
                }
            }
        }
        return $attributes;
    }

    /**
     * @param string $attribute
     * @throws Exception
     * @return void
     */
    public static function addDynamicAttribute($attribute)
    {
        $count = \Yii::$app->db->createCommand()->insert(
            static::dynamicTableName(),
            [
                'attribute' => $attribute,
                'type' => null,
            ]
        )->execute();
        if ($count <= 0) {
            throw new Exception('Не удалось добавить динамический атрибут.');
        }
    }

    /**
     * @param string $attribute
     * @throws Exception
     * @return void
     */
    public static function removeDynamicAttribute($attribute)
    {
        $count = \Yii::$app->db->createCommand()->delete(
            static::dynamicTableName(),
            [
                'attribute' => $attribute,
            ]
        )->execute();

        if ($count <= 0) {
            throw new Exception('Не удалось удалить динамический атрибут.');
        }
    }

    /**
     * @todo стрелять аналогично parent::insertInternal события afterDynamic***, beforeDynamic*** .....
     * @inheritdoc
     */
    protected function insertInternal($attributes = null)
    {
        if (!parent::insertInternal($attributes)) {
            return false;
        }
        $values = $this->getDirtyDynamicAttributes($attributes);
        foreach ($values as $attribute => $value) {
            $rawDynamicAttr = static::getRawDynamicAttr($attribute);
            if (!isset($rawDynamicAttr)) { // не нашли такой атрибут в спрачнике атрибутов к текущей сущности
                return true;
            }
            $oldVal = static::getRawDynamicVal($this->id, $rawDynamicAttr);
            $columns = ['value' => $value];
            if ($this instanceof ActiveRecordHistoryInterface && $this->history) {
                $columns = array_merge($columns, [
                    'history_version' => $this->getNextHistoryVersion(),
                    'history_start_date' => \Yii::$app->formatter->asDatetime(time(), CommonHelper::FORMAT_DATETIME_DB),
                ]);
            }
            if (isset($oldVal)) {
                $count = \Yii::$app->db->createCommand()->update(
                    static::dynamicValueTableName(),
                    $columns,
                    [
                        'dynamic_attribute_id' => $rawDynamicAttr['id'],
                        \Yii::$app->db->schema->getRawTableName(static::tableName()) . '_id' => $this->id
                    ]
                )->execute();
                // TODO
                if (!empty($count) && $this instanceof ActiveRecordHistoryInterface && $this->history) {
//                    $this->saveHistory($this->getDynamicAttributes(), static::historyDynamicTableName());
                }
            } else {
                $columns = array_merge($columns, [
                    'dynamic_attribute_id' => $rawDynamicAttr['id'],
                    \Yii::$app->db->schema->getRawTableName(static::tableName()) . '_id' => $this->id
                ]);
                $count = \Yii::$app->db->createCommand()->insert(
                    static::dynamicValueTableName(),
                    $columns
                )->execute();
                // TODO
                if (!empty($count) && $this instanceof ActiveRecordHistoryInterface && $this->history) {
//                    $this->saveHistory($this->getDynamicAttributes(), static::historyDynamicTableName());
                }
            }
        }
        $this->setOldDynamicAttributes($values);
        return true;
    }

    /**
     * @param array $values
     * @return void
     */
    public function setOldDynamicAttributes($values)
    {
        $this->_oldDynamicAttributes = $values;
    }

    /**
     * @todo стрелять аналогично parent::insertInternal события afterDynamic***, beforeDynamic*** .....
     * @inheritdoc
     */
    protected function updateInternal($attributes = null)
    {
        $result = parent::updateInternal($attributes);
        $values = $this->getDirtyDynamicAttributes($attributes);
        if (empty($values)) {
            return 0;
        }
        foreach ($values as $attribute => $value) {
            $rawDynamicAttr = static::getRawDynamicAttr($attribute);
            if (!isset($rawDynamicAttr)) { // не нашли такой атрибут в спрачнике атрибутов к текущей сущности
                return true;
            }
            $oldVal = static::getRawDynamicVal($this->id, $rawDynamicAttr);
            $columns = ['value' => $value];
            if ($this instanceof ActiveRecordHistoryInterface && $this->history) {
                $columns = array_merge($columns, [
                    'history_version' => $this->getNextHistoryVersion(),
                    'history_start_date' => \Yii::$app->formatter->asDatetime(time(), CommonHelper::FORMAT_DATETIME_DB),
                ]);
            }
            if (isset($oldVal)) {
                $count = \Yii::$app->db->createCommand()->update(
                    static::dynamicValueTableName(),
                    $columns,
                    [
                        'dynamic_attribute_id' => $rawDynamicAttr['id'],
                        \Yii::$app->db->schema->getRawTableName(static::tableName()) . '_id' => $this->id
                    ]
                )->execute();
                // TODO
                if (!empty($count) && $this instanceof ActiveRecordHistoryInterface && $this->history) {
//                    $this->saveHistory($this->getDynamicAttributes(), static::historyDynamicTableName());
                }
            } else {
                $columns = array_merge($columns, [
                    'dynamic_attribute_id' => $rawDynamicAttr['id'],
                    \Yii::$app->db->schema->getRawTableName(static::tableName()) . '_id' => $this->id
                 ]);
                $count = \Yii::$app->db->createCommand()->insert(
                    static::dynamicValueTableName(),
                    $columns
                )->execute();
                // TODO
                if (!empty($count) && $this instanceof ActiveRecordHistoryInterface && $this->history) {
//                    $this->saveHistory($this->getDynamicAttributes(), static::historyDynamicTableName());
                }
            }
        }
        $this->setOldDynamicAttributes($values);
        return $result;
    }

    /**
     * @inheritdoc
     */
    protected function deleteInternal()
    {
        $result = parent::deleteInternal();
        // TODO Поддержать тут нефизическое удаление!!!!!
        // TODO ПОКА ВРЕМЕННО УДАЛЯЕМ ЗАПИСЬ ФИЗИЧЕСКИ!!!!!!!!!!!!!!!!!!!!!!!!
        \Yii::$app
            ->db
            ->createCommand()
            ->delete(
                static::dynamicValueTableName(),
                ['member_id' => $this->id]
            );
        return $result;
    }

    /**
     * @todo typeCast значения по типу dyn__****
     * @param DynamicAttributeInterface $record
     * @return void
     */
    public static function populateDynamicAttributeRecord($record)
    {
        $columns = $record->dynamicAttributes();
        foreach ($columns as $column) {
            $record->_dynamicAttributes[$column] = static::getDynamicAttributeValue($record->id, $column);
        }
        $record->_oldDynamicAttributes = $record->_dynamicAttributes;
    }

    /**
     * @param string $name
     * @return bool
     */
    private function hasDynamicAttribute($name)
    {
        return isset($this->_dynamicAttributes[$name]) || in_array($name, $this->dynamicAttributes(), true);
    }

    /**
     * @param string $attribute
     * @return array|null
     */
    private static function getRawDynamicAttr($attribute)
    {
        $dynAttr = (new Query())
            ->select(['id', 'type'])
            ->from(static::dynamicTableName())
            ->where([
                'attribute' => $attribute,
                'is_deleted' => ActiveRecord::IS_DELETE_FALSE
            ])
            ->one();
        if (empty($dynAttr)) {
            return null;
        }
        return $dynAttr;
    }

    private static function getRawDynamicVal($entityId, $dynAttr)
    {
        if (empty($entityId) || empty($dynAttr)) {
            return null;
        }
        $val = (new Query())
            ->select(['value'])
            ->from(static::dynamicValueTableName())
            ->where([
                \Yii::$app->db->schema->getRawTableName(static::tableName()) . '_id' => $entityId,
                'dynamic_attribute_id' => $dynAttr['id'],
                'is_deleted' => ActiveRecord::IS_DELETE_FALSE
            ])
            ->one();
        if (empty($val)) {
            return null;
        }
        // TODO обязательно сделать TYPECAST в зависимости от type в $dynAttr
        return $val;
    }

    /**
     * @param string $entityId
     * @param string $attribute
     * @return mixed
     */
    public static function getDynamicAttributeValue($entityId, $attribute)
    {
        $v = static::getRawDynamicVal($entityId, static::getRawDynamicAttr($attribute));
        if (empty($v) || empty($v['value'])) {
            return null;
        }
        return $v['value'];
    }

    /**
     * @return array
     */
    public function dynamicAttributes()
    {
        $rows = (new Query())
            ->select(['id', 'attribute'])
            ->from(static::dynamicTableName())
            ->where([
                'is_deleted' => ActiveRecord::IS_DELETE_FALSE
            ])
            ->all();
        return array_values(ArrayHelper::map($rows, 'id', 'attribute'));
    }

    /**
     * @param null $names
     * @param array $except
     * @return array
     */
    public function getDynamicAttributes($names = null, $except = [])
    {
        $values = [];
        if ($names === null) {
            $names = $this->dynamicAttributes();
        }
        foreach ($names as $name) {
            $values[$name] = $this->{$name};
        }
        foreach ($except as $name) {
            unset($values[$name]);
        }

        return $values;
    }

    /**
     * @inheritdoc
     */
    public function __get($name)
    {
        if (isset($this->_dynamicAttributes[$name]) || array_key_exists($name, $this->_dynamicAttributes)) {
            return $this->_dynamicAttributes[$name];
        }
        if ($this->hasDynamicAttribute($name)) {
            return null;
        }
        return parent::__get($name);
    }

    /**
     * @inheritdoc
     */
    public function __set($name, $value)
    {
        if ($this->hasDynamicAttribute($name)) {
            $this->_dynamicAttributes[$name] = $value;
        } else {
            parent::__set($name, $value);
        }
    }

    /**
     * @inheritdoc
     */
    public function canGetProperty($name, $checkVars = true, $checkBehaviors = true)
    {
        if (parent::canGetProperty($name, $checkVars, $checkBehaviors)) {
            return true;
        }

        try {
            return $this->hasDynamicAttribute($name);
        } catch (\Exception $e) {
            // `hasAttribute()` may fail on base/abstract classes in case automatic attribute list fetching used
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function canSetProperty($name, $checkVars = true, $checkBehaviors = true)
    {
        if (parent::canSetProperty($name, $checkVars, $checkBehaviors)) {
            return true;
        }

        try {
            return $this->hasDynamicAttribute($name);
        } catch (\Exception $e) {
            // `hasAttribute()` may fail on base/abstract classes in case automatic attribute list fetching used
            return false;
        }
    }
}
