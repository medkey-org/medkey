<?php
namespace app\seeds;

use app\common\seeds\Seed;

class DashboardEn extends Seed
{
    public function run()
    {
        $this->model = \app\modules\dashboard\models\orm\Dashboard::class;

        $this->data = [
            [
                'key' => 'default',
                'title' => 'Administrator desktop',
                'description' => 'Added via system installation',
                'layout' => 'two_column',
            ],
        ];
    }
}
