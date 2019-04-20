<?php
namespace app\seeds;

use app\common\seeds\Seed;
use app\modules\workplan\models\orm\WorkplanToWeek;

class DefaultWorkplanWeeks extends Seed
{
    public function run()
    {
        $this->model = WorkplanToWeek::class;
        $this->data = [
            [
                'workplan_id' => 1,
                'week' => 1,
            ],
            [
                'workplan_id' => 1,
                'week' => 2,
            ],
            [
                'workplan_id' => 1,
                'week' => 3,
            ],
            [
                'workplan_id' => 1,
                'week' => 4,
            ],
            [
                'workplan_id' => 1,
                'week' => 5,
            ],
            [
                'workplan_id' => 1,
                'week' => 6,
            ],
            [
                'workplan_id' => 1,
                'week' => 7,
            ],
        ];
    }
}
