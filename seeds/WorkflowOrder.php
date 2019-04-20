<?php
namespace app\seeds;

use app\common\seeds\Seed;
use app\modules\config\models\orm\Workflow;

class WorkflowOrder extends Seed
{
    public function run()
    {
        $this->model = Workflow::class;
        $this->data = [
            [
                'orm_module' => 'crm',
                'orm_class' => 'Order',
                'name' => 'Order',
                'type' => Workflow::TYPE_COMMON,
                'status' => 1,
            ],
        ];
    }
}
