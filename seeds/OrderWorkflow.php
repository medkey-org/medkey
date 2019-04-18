<?php
namespace app\seeds;

use app\common\seeds\Seed;
use app\modules\config\models\orm\Workflow;

class OrderWorkflow extends Seed
{
    public function run()
    {
        $this->model = Workflow::class;
        $this->data = [

        ];
    }
}
