<?php
namespace app\seeds;

use app\common\seeds\Seed;
use app\modules\medical\models\orm\Insurance;

class DefaultInsurance extends Seed
{
    public function run()
    {
        $this->model = Insurance::class;
        $this->data = [
            [
                'code' => 9999,
                'title' => 'Default Insurance',
                'short_title' => 'Default Insurance',
            ]
        ];
    }
}
