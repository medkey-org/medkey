<?php
namespace app\modules\config\models\orm;

use app\common\db\ActiveRecord;
use app\modules\config\ConfigModule;
use yii\db\ActiveQueryInterface;

/**
 * Class WorkflowStatus
 *
 * @property string $orm_module
 * @property string $orm_class
 * @property string $state_value
 * @property string $state_alias
 * @property string $state_attribute
 * @property int $status
 * @property bool $is_start
 *
 * @package Module\Config
 * @copyright 2012-2019 Medkey
 */
class WorkflowStatus extends ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;
    const STATE_ATTRIBUTE_DEFAULT = 'status';

    public static function modelIdentity()
    {
        return ['orm_module', 'orm_class', 'state_attribute', 'state_value', 'status'];
    }

    public function init()
    {
        if ($this->isNewRecord) {
            $this->status = self::STATUS_ACTIVE;
        }
        parent::init();
    }

    /**
     * @return array
     */
    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE => 'Активный',
            self::STATUS_INACTIVE => 'Неактивный',
        ];
    }

    /**
     * @return string
     */
    public function getStatusName()
    {
        $statuses = static::statuses();
        return isset($statuses[$this->status]) ? $statuses[$this->status] : null;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ ['orm_module', 'orm_class', 'state_attribute', 'state_value', 'state_alias', 'status', 'is_start'], 'default', 'value' => null ],
            [ 'state_alias', 'string', 'max' => 255 ],
            [ 'is_start', 'default', 'value' => false, ],
            [ 'status', 'default', 'value' => self::STATUS_ACTIVE, ],
            [ ['orm_module', 'orm_class', 'state_attribute', 'state_value', 'state_alias', 'status', 'is_start'], 'required' ],
            [ ['orm_module', 'orm_class', 'state_attribute', 'state_alias'], 'string' ],
            [ ['status', 'is_start', 'state_value'], 'integer' ],
            [ ['state_value'], 'integer', 'min' => 1, 'max' => 99999 ],
            [ ['state_value'],
                'unique',
                'targetAttribute' => [ 'orm_module', 'orm_class', 'state_attribute', 'state_value', 'status' ],
                'filter' => function (ActiveQueryInterface $query) {
                    return $query
                        ->andWhere([
                            '<>',
                            'status',
                            static::STATUS_INACTIVE,
                        ])
                        ->notDeleted();
                }, ],
            [ ['is_start'], 'unique',
                'targetAttribute' => [ 'orm_module', 'orm_class', 'state_attribute', 'status', 'is_start' ],
                'filter' =>function (ActiveQueryInterface $query) {
                    return $query
                        ->andWhere([
                            '<>',
                            'status',
                            static::STATUS_INACTIVE,
                        ])
                        ->andWhere([
                            'is_start' => true,
                        ])
                        ->notDeleted();
                } ],
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
            'state_attribute' => ConfigModule::t('workflow', 'State attribute'),
            'state_value' => ConfigModule::t('workflow', 'State value'),
            'state_alias' => ConfigModule::t('workflow', 'State label'),
            'is_start' => ConfigModule::t('workflow', 'Is start?'),
            'status' => ConfigModule::t('workflow', 'Status'),
        ];
    }
}
