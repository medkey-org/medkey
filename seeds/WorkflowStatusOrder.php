<?php
namespace app\seeds;

use app\common\seeds\Seed;
use app\modules\config\models\orm\WorkflowStatus;

class WorkflowStatusOrder extends Seed
{
    public function run()
    {
        $this->model = WorkflowStatus::class;
        $this->data = [
            [
                'orm_module' => 'crm',
                'orm_class' => 'Order',
                'state_attribute' => WorkflowStatus::STATE_ATTRIBUTE_DEFAULT,
                'state_value' => 1,
                'state_alias' => 'NEW',
                'status' => WorkflowStatus::STATUS_ACTIVE,
                'is_start' => true,
            ],
            [
                'orm_module' => 'crm',
                'orm_class' => 'Order',
                'state_attribute' => WorkflowStatus::STATE_ATTRIBUTE_DEFAULT,
                'state_value' => 2,
                'state_alias' => 'PAID',
                'status' => WorkflowStatus::STATUS_ACTIVE,
                'is_start' => 0,
            ],
        ];
    }
}
