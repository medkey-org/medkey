<?php
namespace app\seeds;

use app\common\seeds\Seed;
use app\modules\medical\models\orm\Speciality;

class DefaultSpeciality extends Seed
{
    public function run()
    {
        $this->model = Speciality::class;
        $this->data = [
            [
                'title' => 'Default Doctor',
                'short_title' => 'Default Doctor',
            ]
        ];
    }
}
