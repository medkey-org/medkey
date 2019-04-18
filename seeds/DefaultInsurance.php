<?php
namespace app\seeds;

use app\common\seeds\Seed;
use app\modules\medical\models\orm\Insurance;

/**
 * Class AclRoleSeed
 * @package Seed
 * @copyright 2012-2019 Medkey
 */
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
