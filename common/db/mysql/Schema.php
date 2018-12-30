<?php
namespace app\common\db\mysql;

/**
 * Class Schema
 * @package Common\DB
 */
class Schema extends \yii\db\mysql\Schema
{
    /**
     * @inheritdoc
     */
    public function insert($table, $columns)
    {
        $existsIdColumn = $this->getTableSchema($table);
        if ($existsIdColumn) {
            $existsIdColumn = $existsIdColumn->getColumn('id');
        }
        if ($existsIdColumn && empty($columns['id'])) {
            $uuid = \Yii::$app->db->createCommand('SELECT UUID();')->queryScalar();
            $columns['id'] = $uuid;
        }
        $command = $this->db->createCommand()->insert($table, $columns);
        if (!$command->execute()) {
            return false;
        }
        $tableSchema = $this->getTableSchema($table);
        $result = [];
        foreach ($tableSchema->primaryKey as $name) {
            if ($tableSchema->columns[$name]->autoIncrement) {
                $result[$name] = $this->getLastInsertID($tableSchema->sequenceName);
                break;
            } else {
                $result[$name] = isset($columns[$name]) ? $columns[$name] : $tableSchema->columns[$name]->defaultValue;
            }
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    protected function defaultConditionClasses()
    {
        return array_merge(parent::defaultConditionClasses(), [
            'ILIKE' => 'yii\db\conditions\LikeCondition',
            'NOT ILIKE' => 'yii\db\conditions\LikeCondition',
            'OR ILIKE' => 'yii\db\conditions\LikeCondition',
            'OR NOT ILIKE' => 'yii\db\conditions\LikeCondition',
        ]);
    }
}
