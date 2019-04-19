<?php
namespace app\seeds;

use app\common\seeds\Seed;
use app\modules\medical\models\orm\Patient;

class DefaultPatients extends Seed
{
    public function run()
    {
        $this->model = Patient::class;
        $this->data = [
            [
                'first_name' => 'John',
                'last_name' => 'Smith',
                'birthday' => '1975-02-02',
                'sex' => Patient::SEX_MALE,
            ],
            [
                'first_name' => 'Alice',
                'last_name' => 'Thomson',
                'birthday' => '1990-04-04',
                'sex' => Patient::SEX_FEMALE,
            ]
        ];
    }
}
