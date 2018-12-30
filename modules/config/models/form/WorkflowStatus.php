<?php
namespace app\modules\config\models\form;

use app\common\base\Model;
use app\modules\config\models\orm\WorkflowStatus as WorkflowStatusORM;
use yii\db\ActiveQueryInterface;

/**
 * Class WorkflowStatus
 * @package Module\Config
 * @copyright 2012-2019 Medkey
 */
class WorkflowStatus extends Model
{
    public $id;
    public $orm_module;
    public $orm_class;
    public $state_attribute;
    public $state_value;
    public $state_alias;
    public $status;
    public $is_start;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ ['orm_module', 'orm_class', 'state_attribute', 'state_value', 'state_alias', 'status', 'is_start'], 'default', 'value' => null ],
            [ 'state_alias', 'string', 'max' => 255 ],
            [ 'is_start', 'default', 'value' => false, ],
            [ 'status', 'default', 'value' => WorkflowStatusORM::STATUS_ACTIVE],
            [ ['orm_module', 'orm_class', 'state_attribute', 'state_value', 'state_alias', 'status', 'is_start'], 'required' ],
            [ ['orm_module', 'orm_class', 'state_attribute', 'state_alias'], 'string' ],
            [ ['status', 'is_start', 'state_value'], 'integer' ],
            [ ['state_value'], 'integer', 'min' => 1, 'max' => 99999 ],
            [ ['state_value'],
                'unique',
                'targetClass' => WorkflowStatusORM::class,
                'targetAttribute' => [ 'orm_module', 'orm_class', 'state_attribute', 'state_value', 'status' ],
                'filter' => function (ActiveQueryInterface $query) {
                    return $query
                        ->andWhere([
                            '<>',
                            'status',
                            WorkflowStatusORM::STATUS_INACTIVE,
                        ])
                        ->andFilterWhere([
                            '<>',
                            'id',
                            $this->id,
                        ])
                        ->notDeleted();
                }, ],
            [ ['is_start'], 'unique',
                'targetClass' => WorkflowStatusORM::class,
                'targetAttribute' => ['orm_module', 'orm_class', 'state_attribute', 'status', 'is_start'],
                'filter' =>function (ActiveQueryInterface $query) {
                    return $query
                        ->andWhere([
                            '<>',
                            'status',
                            WorkflowStatusORM::STATUS_INACTIVE,
                        ])
                        ->andWhere([
                            'is_start' => true,
                        ])
                        ->andFilterWhere([
                            '<>',
                            'id',
                            $this->id,
                        ])
                        ->notDeleted();
                } ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'orm_module' => 'Entity module',
            'orm_class' => 'Entity',
            'state_attribute' => 'Status attribute',
            'state_value' => 'Status value',
            'state_alias' => 'Displaying status value',
            'is_start' => 'Is started',
            'status' => 'Status',
        ];
    }
}
