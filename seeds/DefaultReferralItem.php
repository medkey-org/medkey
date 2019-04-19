<?php
namespace app\seeds;

use app\common\helpers\ArrayHelper;
use app\common\seeds\Seed;
use app\modules\medical\models\orm\ReferralItem;

class DefaultReferralItem extends Seed
{
    public function run()
    {
        $defaultReferral = $this->call('default_referral')->models;
        $defaultService = $this->call('default_service')->models;
        $this->model = ReferralItem::class;
        $this->data = [
            [
                'referral_id' => ArrayHelper::findBy($defaultReferral, ['number' => '1'])->id,
                'service_id' => ArrayHelper::findBy($defaultService, ['code' => '1'])->id,
            ],
        ];
    }
}
