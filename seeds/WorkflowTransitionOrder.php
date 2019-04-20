<?php
namespace app\seeds;

use app\common\seeds\Seed;
use app\modules\config\models\orm\WorkflowTransition;

class WorkflowTransitionOrder extends Seed
{
    public function run()
    {
        $this->model = WorkflowTransition::class;
        $this->data = [
            [
                'workflow_id' => 1,
                'name' => 'PAID',
                'from_id' => 1,
                'to_id' => 2,
                'handler_type' => 'OrderHandlerInterface',
                'handler_method' => 'onPaid',
                'middleware' => true,
            ]
        ];
    }
}
