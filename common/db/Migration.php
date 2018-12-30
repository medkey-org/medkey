<?php
namespace app\common\db;

use yii\db\ColumnSchemaBuilder;
use yii\db\Expression;

/**
 * Class Migration
 * @package Common\DB
 * @copyright 2012-2019 Medkey
 */
class Migration extends \yii\db\Migration
{
    use SchemaBuilderTrait;

    /**
     * Values: 'bigint', 'uuid'
     * @var string
     */
    public $pkType = 'bigint';
    /**
     * @var string
     */
    private $pkColumn = 'id';
    /**
     * @var array
     */
    private $_tableFields = [];
    /**
     * @var array
     */
    private $_historyTableFields = [];
    /**
     * @var array
     */
    public $tableOptions = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (getenv('APP_TYPE_KEY')) {
            $this->pkType = getenv('APP_TYPE_KEY');
        }
        $this->tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $this->tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->_tableFields = [
            'user_created_id' => $this->foreignKeyId(),
            'user_updated_id' => $this->foreignKeyId(),
            'history_version' => $this->bigInteger()->defaultValue(1),
            'history_start_date' => $this->timestamp()->notNull()->defaultValue(new Expression('NOW()')),
            'created_at' => $this->timestamp()->notNull()->defaultValue(new Expression('NOW()')),
            'updated_at' => $this->timestamp()->notNull()->defaultValue(new Expression('NOW()')),
            'deleted_at' => $this->timestamp(),
            'is_deleted' => $this->integer()->defaultValue(0), // for index unique
        ];
        $this->_historyTableFields = [
            'history_end_date' => $this->timestamp()->notNull()->defaultValue(new Expression('NOW()')),
        ];
        parent::init();
    }

    /**
     * @param string $rawTable
     * @return void
     */
    public function alterTableUUID($rawTable)
    {
        $qTable = $this->db->schema->quoteTableName($rawTable);
        if (($this->db->driverName === 'mysql' || $this->db->driverName === 'mysqli') && $rawTable !== 'migration') {
            $sql = <<<SQL
CREATE TRIGGER before_insert_{$rawTable}
  BEFORE INSERT ON {$qTable}
  FOR EACH ROW
  IF new.{$this->pkColumn} IS NULL THEN SET new.{$this->pkColumn} = uuid();
  ELSEIF new.{$this->pkColumn} = '' THEN SET new.{$this->pkColumn} = uuid();
  END IF;
SQL;
            $this->db->createCommand($sql)->execute();
        } elseif ($this->db->driverName === 'pgsql' && $rawTable !== 'migration') {
            $sql = <<<SQL
ALTER TABLE $qTable ALTER COLUMN {$this->pkColumn} SET DEFAULT uuid_generate_v1();
SQL;
            $this->db->createCommand($sql)->execute();
        } elseif ($this->db->driverName === 'mssql') {
            // todo реализовать
        }
    }

    public function executeExpression($sqlExpression)
    {
        if (is_string($sqlExpression)) {
            $this->db->createCommand($sqlExpression)->execute();
        } elseif (is_array($sqlExpression)) {
            foreach ($sqlExpression as $exp) {
                $this->db->createCommand($exp)->execute();
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function createTable($table, $columns, $options = null)
    {
        $rawTable = $this->db->schema->getRawTableName($table);
        if (empty($options)) {
            $options = $this->tableOptions;
        }
        $columns = array_merge($columns, $this->_tableFields);
        if ($this->pkType === 'bigint') {
            $columns[$this->pkColumn] = $this->bigPrimaryKey();
            parent::createTable($table, $columns, $options);
        } elseif ($this->pkType === 'uuid') {
            $columns[$this->pkColumn] = $this->string(36);
            parent::createTable($table, $columns, $options);
            $this->addPrimaryKey('pk_' . $this->pkColumn . '_' . $rawTable, $table, $this->pkColumn);
            $this->alterTableUUID($table);
        }
    }

    /**
     * @param string $table
     * @param array $columns
     * @return void
     */
    public function createHistoryTable($table, $columns)
    {
        $rawTable = $this->db->schema->getRawTableName($table);
        $table = $this->getHistoryTable($table);
        $columns = array_merge($columns, $this->_tableFields);
        $columns = array_merge($columns, $this->_historyTableFields);
        if ($this->pkType === 'bigint') {
            $columns[$this->pkColumn] = $this->bigInteger();
        } elseif ($this->pkType === 'uuid') {
            $columns[$this->pkColumn] = $this->string(36);
        }
        parent::createTable($table, $columns);
        $this->addPrimaryKey('pk_' . $this->pkColumn . 'h_v' . $rawTable, $table, [$this->pkColumn, 'history_version']);
    }

    /**
     * @param string $table
     * @return void
     */
    public function createResponsibilityTable($table)
    {
        $rawTable = $this->db->schema->getRawTableName($table);
        $columns = [
            'employee_id' => $this->foreignKeyId(),
            'is_main' => $this->boolean(),
            $rawTable . '_id' => $this->foreignKeyId()
        ];
        $columns = array_merge($columns, $this->_tableFields);
        $this->createTable($this->getResponsibilityTable($table), $columns);
        $this->createHistoryTable($this->getResponsibilityTable($table), $columns);
    }

    /**
     * @param string $table
     * @return void
     */
    public function createPositionTable($table)
    {
        $rawTable = $this->db->schema->getRawTableName($table);
        $columns = [
            'position_id' => $this->foreignKeyId(),
            $rawTable . '_id' => $this->foreignKeyId()
        ];
        $columns = array_merge($columns, $this->_tableFields);
        $this->createTable($this->getPositionTable($table), $columns);
        $this->createHistoryTable($this->getPositionTable($table), $columns);
    }

    /**
     * @param string $table
     * @return void
     */
    public function createDepartmentTable($table)
    {
        $rawTable = $this->db->schema->getRawTableName($table);
        $columns = [
            'department_id' => $this->foreignKeyId(),
            $rawTable . '_id' => $this->foreignKeyId()
        ];
        $columns = array_merge($columns, $this->_tableFields);
        $this->createTable($this->getDepartmentTable($table), $columns);
        $this->createHistoryTable($this->getDepartmentTable($table), $columns);
    }

    /**
     * @param string $table
     * @see \yii\db\Migration::dropTable()
     * @return void
     */
    public function dropHistoryTable($table)
    {
        parent::dropTable($this->getHistoryTable($table));
    }

    /**
     * @param string $table
     * @see \yii\db\Migration::dropTable()
     * @return void
     */
    public function dropResponsibilityTable($table)
    {
        parent::dropTable($this->getResponsibilityTable($table));
        $this->dropHistoryTable($this->getResponsibilityTable($table));
    }

    /**
     * @param string $table
     * @see \yii\db\Migration::dropTable()
     * @return void
     */
    public function dropPositionTable($table)
    {
        parent::dropTable($this->getPositionTable($table));
        $this->dropHistoryTable($this->getPositionTable($table));
    }

    /**
     * @param string $table
     * @see \yii\db\Migration::dropTable()
     * @return void
     */
    public function dropDepartmentTable($table)
    {
        parent::dropTable($this->getDepartmentTable($table));
        $this->dropHistoryTable($this->getDepartmentTable($table));
    }

    /**
     * @todo возможно можно вынести в конфигрурацию настройку
     * @param string $table
     * @return string
     */
    protected function getHistoryTable($table)
    {
        return '{{%his__' . $this->db->schema->getRawTableName($table) . '}}';
    }

    /**
     * @todo возможно можно вынести в конфигрурацию настройку
     * @param string $table
     * @return string
     */
    protected function getDepartmentTable($table)
    {
        return '{{%dep__' . $this->db->schema->getRawTableName($table) . '}}';
    }

    /**
     * @param string $table
     * @return string
     */
    protected function getResponsibilityTable($table)
    {
        return '{{%res__' . $this->db->schema->getRawTableName($table) . '}}';
    }

    /**
     * @param string $table
     * @return string
     */
    protected function getPositionTable($table)
    {
        return '{{%pos__' . $this->db->schema->getRawTableName($table) . '}}';
    }

    /**
     * @todo check сущест. динамич. атрибутов в БД и проверять уже существ. атрибуты
     * @inheritdoc
     */
    public function addColumn($table, $column, $type)
    {
        $time = $this->beginCommand("add column $column $type to table $table");
        $this->db->createCommand()->addColumn($table, $column, $type)->execute();
        if ($type instanceof ColumnSchemaBuilder && $type->comment !== null) {
            $this->db->createCommand()->addCommentOnColumn($table, $column, $type->comment)->execute();
        }
        $this->endCommand($time);
    }

    /**
     * @param string $table
     * @param string $column
     * @param string|\yii\db\ColumnSchemaBuilder $type
     */
    public function addHistoryColumn($table, $column, $type)
    {
        $table = $this->getHistoryTable($table);
        $this->addColumn($table, $column, $type);
    }

    /**
     * @param string $table
     * @param string $column
     * @return void
     */
    public function dropHistoryColumn($table, $column)
    {
        $this->dropColumn($this->getHistoryTable($table), $column);
    }

    /**
     * @param string $table
     * @param string $name
     * @param string $newName
     * @return void
     */
    public function renameHistoryColumn($table, $name, $newName)
    {
        $this->renameColumn($this->getHistoryTable($table), $name, $newName);
    }

    /**
     * @param string $table
     * @param string $column
     * @param string $type
     * @return void
     */
    public function alterHistoryColumn($table, $column, $type)
    {
        $this->alterColumn($this->getHistoryTable($table), $column, $type);
    }

    /**
     * @param string $table
     * @param ColumnSchemaBuilder[] $columns
     * @param string $options
     * @return void
     */
    public function createBothTables($table, $columns, $options = null)
    {
        $this->createTable($table, $columns, $options);
        $this->createHistoryTable($table, $columns);
    }

    /**
     * @param string $table
     * @return void
     */
    public function dropBothTables($table)
    {
        $this->dropTable($table);
        $this->dropHistoryTable($table);
    }

    /**
     * @param string $table
     * @param ColumnSchemaBuilder[] $columns
     * @param string $options
     * @return void
     */
    public function createAllTables($table, $columns, $options = null)
    {
        $this->createTable($table, $columns, $options);
        $this->createHistoryTable($table, $columns);
        $this->createPositionTable($table);
        $this->createResponsibilityTable($table);
        $this->createDepartmentTable($table);
    }

    /**
     * @param string $table
     * @return void
     */
    public function dropAllTables($table)
    {
        $this->dropTable($table);
        $this->dropHistoryTable($table);
        $this->dropResponsibilityTable($table);
        $this->dropPositionTable($table);
        $this->dropDepartmentTable($table);
    }

    /**
     * @param string $table
     * @param string $column
     * @param ColumnSchemaBuilder $type
     * @return void
     */
    public function addBothColumns($table, $column, ColumnSchemaBuilder $type)
    {
        $this->addColumn($table, $column, $type);
        $this->addHistoryColumn($table, $column, $type);
    }

    /**
     * @param string $table
     * @param string $column
     * @return void
     */
    public function dropBothColumns($table, $column)
    {
        $this->dropColumn($table, $column);
        $this->dropHistoryColumn($table, $column);
    }

    /**
     * @param string $table
     * @param string $name
     * @param string $newName
     * @return void
     */
    public function renameBothColumns($table, $name, $newName)
    {
        $this->renameColumn($table, $name, $newName);
        $this->renameHistoryColumn($table, $name, $newName);
    }
}
