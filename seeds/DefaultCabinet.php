<?php
namespace app\seeds;

use app\common\seeds\Seed;
use app\modules\organization\models\orm\Cabinet;

class DefaultCabinet extends Seed
{
    public function run()
    {
        $this->model = Cabinet::class;
        $this->data = [
            [
                'number' => '9999',
                'description' => 'Desc',
                'organization_id' => 1,
                'department_id' => 1,
            ]
        ];
    }
}