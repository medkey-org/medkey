<?php
namespace app\seeds;

use app\common\seeds\Seed;
use app\modules\medical\models\orm\Service;

class DefaultService extends Seed
{
    public function run()
    {
        $this->model = Service::class;
        $this->data = [
            [
                'code' => "1",
                'title' => 'Default Title Service',
                'short_title' => 'Default Short Title Service',
                'description' => 'Description',
                'speciality_id' => 1,
            ],
        ];
    }
}
