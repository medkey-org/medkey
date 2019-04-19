<?php
namespace app\seeds;

use app\common\seeds\Seed;
use app\modules\config\models\orm\Workflow;

/**
 * Class AclRoleSeed
 * @package Seed
 * @copyright 2012-2019 Medkey
 */
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
