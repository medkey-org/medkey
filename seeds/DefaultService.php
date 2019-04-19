<?php
namespace app\seeds;

use app\common\seeds\Seed;
use app\modules\medical\models\orm\Service;

/**
 * Class AclRoleSeed
 * @package Seed
 * @copyright 2012-2019 Medkey
 */
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
