<?php
namespace app\common\db;

use app\common\helpers\CommonHelper;
use app\common\helpers\Json;

/**
 * Class ActiveRecordHistoryTrait
 * @mixin ActiveRecord
 * @package Common\DB
 * @copyright 2012-2019 Medkey
 */
trait ActiveRecordHistoryTrait
{
    /**
     * @var bool
     */
    public $history = true;

    /**
     * @return string
     */
    public static function historyTableName()
    {
        return '{{%his__' . \Yii::$app->db->schema->getRawTableName(static::tableName()) . '}}';
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $this->loadDefaultValues();
        return true;
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        // @see updateInternal. Данный блок кода нужен из-за того, что "настоящий" UPDATE
        // не отрабатывает, если нет ничего в changedAttributes, соответственно не инкрементируется
        // history_version (optimisticLock())
        if (!$insert && empty($changedAttributes)) {
            \Yii::warning('Double saving without changed attributes.');
            return parent::afterSave($insert, $changedAttributes);
        }
        if ($this->history && !$this->saveHistory($this->getAttributes(), static::historyTableName())) {
            throw new Exception(\Yii::t('app', 'Transaction is canceled: History saving error.'));
        }
        parent::afterSave($insert, $changedAttributes);
    }

    public function deleteHistory()
    {
        $this->is_deleted = self::IS_DELETE_TRUE;
        $this->deleted_at = \Yii::$app->formatter->asDatetime(time(), CommonHelper::FORMAT_DATETIME_DB);
        if (!$this->save(false)) {
            $errors = Json::encode($this->getErrors());
            \Yii::warning('Не удалось удалить запись. Причина: ' . $errors);
            return false;
        }
        return true;
    }

    /**
     * @param array $attributes
     * @param string $tableName
     * @return int
     * @throws \yii\db\Exception
     */
    public function saveHistory($attributes, $tableName)
    {
        return \Yii::$app->db->createCommand()->insert($tableName, $attributes)->execute();
    }
}
