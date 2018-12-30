<?php
namespace app\modules\config\models\form;

use app\common\base\Model;
use app\common\validators\ForeignKeyValidator;
use app\modules\config\models\orm\WorkflowTransition as WorkflowTransitionORM;
use yii\db\ActiveQueryInterface;

/**
 * Class WorkflowTransition
 * @package Module\Config
 * @copyright 2012-2019 Medkey
 */
class WorkflowTransition extends Model
{
    public $id;
    public $workflow_id;
    public $name;
    public $from_id;
    public $to_id;
    public $handler_type;
    public $handler_method;
    public $workflow_module;
    public $middleware;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ ['middleware'], 'boolean', ],
            [ ['from_id', 'to_id', 'workflow_id'], 'default', 'value' => null ], // fix for DB exc INTEGER value
            [ ['workflow_id', 'from_id', 'to_id'], ForeignKeyValidator::class, ],
            [ ['name', 'workflow_id', 'from_id', 'to_id'], 'required', ],
//            [ ['workflow_id', 'from_id', 'to_id'], 'integer' ],
            [ ['name', 'handler_type', 'handler_method'], 'string' ],
            [ [ 'from_id', 'to_id' ],
                'unique',
                'targetClass' => WorkflowTransitionORM::class,
                'targetAttribute' => ['workflow_id', 'from_id', 'to_id'],
                'filter' => function (ActiveQueryInterface $query) {
                    return $query
                        ->andFilterWhere([
                            '<>',
                            'id',
                            $this->id
                        ])
                        ->notDeleted();
                },]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'workflow_id' => 'ЖЦ',
            'name' => 'Наименование перехода',
            'from_id' => 'Начальный статус',
            'to_id' => 'Конечный статус',
            'handler_type' => 'Тип-обработчик перехода',
            'handler_method' => 'Функция-обработчик перехода',
            'middleware' => 'Middleware',
        ];
    }
}
