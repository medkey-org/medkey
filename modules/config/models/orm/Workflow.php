<?php
namespace app\modules\config\models\orm;

use app\common\db\ActiveRecord;
use app\modules\config\ConfigModule;
use yii\db\ActiveQueryInterface;

/**
 * Class Workflow
 *
 * @property string $name
 * @property int $status
 * @property-read WorkflowTransition workflowStartTransition
 * @property-read WorkflowTransition workflowTransitionsWithoutStart
 *
 * @package Module\Config
 * @copyright 2012-2019 Medkey
 */
class Workflow extends ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_UPDATING = 2;
    const STATUS_ARCHIVE = 3;

    const TYPE_COMMON = 1;

    public static function modelIdentity()
    {
        return ['orm_module', 'orm_class', 'status'];
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->isNewRecord) {
            $this->status = self::STATUS_UPDATING;
        }
        parent::init();
    }

    public function getStatusName()
    {
        $statuses = static::statuses();
        return isset($statuses[$this->status]) ? $statuses[$this->status] : null;
    }

    /**
     * @return array
     */
    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE => ConfigModule::t('workflow', 'Active'),
            self::STATUS_UPDATING => ConfigModule::t('workflow', 'Editing'),
            self::STATUS_ARCHIVE => ConfigModule::t('workflow', 'Archive'),
        ];
    }

    public function getWorkflowTransitions()
    {
        return $this->hasMany(WorkflowTransition::class, ['workflow_id' => 'id']);
    }

    public function getWorkflowTransitionsWithoutStart()
    {
        return $this->hasMany(WorkflowTransition::class, ['workflow_id' => 'id'])
            ->joinWith('statusFrom', true)
            ->where([
                WorkflowStatus::tableColumns('is_start') => false,
            ]);
    }

    public function getWorkflowStartTransition()
    {
        return $this->hasOne(WorkflowTransition::class, ['workflow_id' => 'id'])
            ->joinWith('statusFrom', true)
            ->where([
                WorkflowStatus::tableColumns('is_start') => true,
            ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ ['orm_module', 'orm_class', 'name', 'status'], 'required' ],
            [ 'name', 'string', 'max' => 255 ],
            [ ['orm_class'],
                'unique',
                'targetAttribute' => ['orm_module', 'orm_class', 'status'],
                'filter' => function (ActiveQueryInterface $query) {
                    return $query
                        ->andWhere([
                            '<>',
                            'status',
                            static::STATUS_ARCHIVE, // смотрим unique только у активных воркфлоу
                        ])
                        ->notDeleted();
                }, ],
            [ ['orm_module', 'orm_class', 'name'], 'string' ],
            [ ['type', 'status'], 'integer' ],
            [ 'type', 'default', 'value' => self::TYPE_COMMON ],
            [ 'status', 'default', 'value' => self::STATUS_UPDATING ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabelsOverride()
    {
        return [
            'orm_module' => ConfigModule::t('workflow', 'Module'),
            'orm_class' => ConfigModule::t('workflow', 'Entity'),
            'name' => ConfigModule::t('workflow', 'Name'),
            'type' => ConfigModule::t('workflow', 'Type'),
            'status' => ConfigModule::t('workflow', 'Status'),
        ];
    }
}
