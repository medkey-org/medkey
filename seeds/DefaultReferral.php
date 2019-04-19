<?php
namespace app\seeds;

use app\common\helpers\ArrayHelper;
use app\common\seeds\Seed;
use app\modules\medical\models\orm\Referral;

class DefaultReferral extends Seed
{
    public function run()
    {
        $defaultEHR = $this->call('default_ehr')->models;
        $this->model = Referral::class;
        $this->data = [
            [
                'number' => '1',
                'description' => 'desc',
                'status' => Referral::STATUS_ACTIVE,
                'start_date' => '2019-01-01',
                'end_date' => '2023-01-01',
                'ehr_id' => ArrayHelper::findBy($defaultEHR, ['number' => '1'])->id,
            ]
        ];
    }
}
